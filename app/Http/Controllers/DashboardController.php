<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TestResult;
use App\Models\LabRequestItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // Force locale setting for debugging
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // 1. Alert Banners
        $delayedRequestsCount = LabRequest::where('status', '!=', 'completed')
            ->where('created_at', '<', Carbon::now()->subHours(48))
            ->count();

        $abnormalResultsTodayCount = TestResult::whereDate('created_at', $today)
            ->whereIn('flag', ['High', 'Low'])
            ->count();

        // 3. Inventory Alerts
        $lowStockCount = \App\Models\InventoryItem::lowStock()->count();
        $expiringSoonCount = \App\Models\InventoryItem::expiringSoon(30)->count();

        // 2. Core Summary Stats
        $stats = [
            'total_requests' => LabRequest::count(),
            'completed_today' => LabRequest::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'pending_requests' => LabRequest::whereIn('status', ['pending', 'collected', 'sample_collected', 'in_progress', 'review'])->count(),
            'revenue_today' => Payment::whereDate('paid_at', $today)->where('status', 'paid')->sum('amount'),
            'revenue_this_month' => Payment::whereBetween('paid_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('status', 'paid')->sum('amount'),
            'unpaid_amount' => Payment::where('status', 'unpaid')->sum('amount'),
            'cash_collected_today' => Payment::whereDate('paid_at', $today)->where('status', 'paid')->sum('amount'),
        ];

        // 3. Smart Reporting & Predictions
        // Compare Last 30 Days with Previous 30 Days
        $requestsLastMonth = LabRequest::whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count();
        $requestsThisMonth = LabRequest::where('created_at', '>=', $thirtyDaysAgo)->count();
        $growth = $requestsLastMonth > 0 ? (($requestsThisMonth - $requestsLastMonth) / $requestsLastMonth) * 100 : 0;
        
        $prediction = [
            'expected_next_month' => round($requestsThisMonth * 1.1),
            'growth' => round($growth, 1),
        ];

        // Overall Abnormal Rate
        $allResults30Days = TestResult::where('created_at', '>=', $thirtyDaysAgo)->count();
        $abnormalResults30Days = TestResult::where('created_at', '>=', $thirtyDaysAgo)->whereIn('flag', ['High', 'Low'])->count();
        $abnormalRate = $allResults30Days > 0 ? ($abnormalResults30Days / $allResults30Days) * 100 : 0; 

        // Trends & Abnormal Rate Per Test (last 30 days)
        $testTrends = DB::table('lab_request_items')
            ->join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->leftJoin('test_results', 'lab_request_items.id', '=', 'test_results.request_item_id')
            ->select(
                'tests.name as test_name',
                DB::raw('COUNT(lab_request_items.id) as total_this_month'),
                DB::raw('SUM(CASE WHEN test_results.flag IN ("High", "Low") THEN 1 ELSE 0 END) as abnormal_count')
            )
            ->where('lab_request_items.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('tests.id', 'tests.name')
            ->having('total_this_month', '>', 0)
            ->orderByDesc('total_this_month')
            ->limit(5)
            ->get();

        $testPreviousMonth = DB::table('lab_request_items')
            ->join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->select('tests.name as test_name', DB::raw('COUNT(lab_request_items.id) as total_last_month'))
            ->whereBetween('lab_request_items.created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])
            ->groupBy('tests.id', 'tests.name')
            ->get()->keyBy('test_name');

        $insights = [];
        $detailedPredictions = [];

        foreach ($testTrends as $trend) {
            $lastMonthCount = $testPreviousMonth->get($trend->test_name)->total_last_month ?? 0;
            $percentChange = $lastMonthCount > 0 ? (($trend->total_this_month - $lastMonthCount) / $lastMonthCount) * 100 : 100;
            $abnVRate = $trend->total_this_month > 0 ? ($trend->abnormal_count / $trend->total_this_month) * 100 : 0;

            $detailedPredictions[] = [
                'test_name' => $trend->test_name,
                'current_month' => $trend->total_this_month,
                'last_month' => $lastMonthCount,
                'trend_percent' => round($percentChange, 1),
                'abnormal_rate' => round($abnVRate, 1),
                'prediction_next_month' => round($trend->total_this_month * (1 + ($percentChange / 100)))
            ];

            // Generate Insights based on data
            if ($percentChange > 20 && strtolower($trend->test_name) == 'glucose') {
                $insights[] = "Glucose tests increasing by " . round($percentChange) . "% -> possible diabetes season trend.";
            } elseif ($percentChange > 15 && stripos($trend->test_name, 'cbc') !== false) {
                $insights[] = "CBC tests up by " . round($percentChange) . "% -> watch for potential outbreak infections.";
            } elseif ($abnVRate > 30) {
                $insights[] = "High abnormal rate (" . round($abnVRate) . "%) in " . $trend->test_name . " -> consider QC check or protocol review.";
            }
        }

        // Additional Smart Insights
        if ($stats['pending_requests'] > 20) {
            $insights[] = "High pending requests (" . $stats['pending_requests'] . ") -> consider staffing adjustments.";
        }

        if ($stats['unpaid_amount'] > 1000) {
            $insights[] = "Outstanding payments: " . app_currency($stats['unpaid_amount']) . " -> follow up on collections.";
        }

        // Detect abnormal spikes in specific tests
        $abnormalSpikes = TestResult::whereDate('test_results.created_at', $today)
            ->whereIn('flag', ['High', 'Low'])
            ->join('lab_request_items', 'test_results.request_item_id', '=', 'lab_request_items.id')
            ->join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->select('tests.name', DB::raw('COUNT(*) as abnormal_count'))
            ->groupBy('tests.id', 'tests.name')
            ->having('abnormal_count', '>', 5)
            ->get();

        foreach ($abnormalSpikes as $spike) {
            $insights[] = "Abnormal spike in " . $spike->name . " (" . $spike->abnormal_count . " cases today) -> investigate quality issues.";
        }

        // Inventory shortage prediction
        $criticalStock = \App\Models\InventoryItem::where('current_stock', '<=', 10)->count();
        if ($criticalStock > 0) {
            $insights[] = $criticalStock . " inventory items critically low -> reorder immediately.";
        }

        // Fill default insight if none generated
        if(empty($insights)){
            $insights[] = "Testing volume shows expected seasonal variance. Monitoring overall QC.";
        }

        // 4. Highest Demand Tests (kept for legacy support)
        $highestDemandTests = LabRequestItem::select('test_id', DB::raw('count(*) as count'))
            ->groupBy('test_id')
            ->orderBy('count', 'desc')
            ->with('test')
            ->take(3)
            ->get();

        // 5. Weekly Chart Data (Operations & Revenue Trend)
        $chartDates = [];
        $requestCounts = [];
        $revenueCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::now()->subDays($i)->format('M d');
            
            $requestCounts[] = LabRequest::whereDate('created_at', $date)->count();
            $revenueCounts[] = (float)Payment::whereDate('paid_at', $date)->where('status', 'paid')->sum('amount');
        }

        // 6. Test Distribution (30 Days) - Top 5 categories
        $distributionData = LabRequestItem::join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->join('test_categories', 'tests.category_id', '=', 'test_categories.id')
            ->select('test_categories.name', DB::raw('count(*) as count'))
            ->where('lab_request_items.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('test_categories.name')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $distLabels = $distributionData->pluck('name');
        $distValues = $distributionData->pluck('count');

        // Additional Analytics for Manager Dashboard
        $topTests = DB::table('lab_request_items')
            ->join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->select('tests.name', DB::raw('COUNT(*) as count'))
            ->where('lab_request_items.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('tests.id', 'tests.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Peak days analysis (requests per day of week)
        $peakDays = DB::table('lab_requests')
            ->select(DB::raw('DAYOFWEEK(created_at) as day_of_week'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('day_of_week')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $item->day_name = $days[$item->day_of_week - 1] ?? 'Unknown';
                return $item;
            });

        // Inventory consumption rate
        $inventoryConsumption = DB::table('inventory_transactions')
            ->select('inventory_item_id', DB::raw('SUM(quantity) as total_used'))
            ->where('type', 'out')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('inventory_item_id')
            ->orderByDesc('total_used')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats', 
            'delayedRequestsCount', 
            'abnormalResultsTodayCount', 
            'lowStockCount',
            'expiringSoonCount',
            'prediction', 
            'detailedPredictions',
            'insights',
            'abnormalRate', 
            'highestDemandTests',
            'chartDates',
            'requestCounts',
            'revenueCounts',
            'distLabels',
            'distValues',
            'topTests',
            'peakDays',
            'inventoryConsumption'
        ));
    }
}
