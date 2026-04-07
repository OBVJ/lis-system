<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\Payment;
use App\Models\TestResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function operational(Request $request)
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay();

        $last30 = LabRequest::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $pending = LabRequest::whereIn('status', ['pending', 'collected', 'sample_collected', 'in_progress', 'review'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();
        $completed = LabRequest::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();
        $delayed = LabRequest::where('status', '!=', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('created_at', '<', now()->subHours(48))
            ->count();

        return view('reports.operational', compact('last30', 'pending', 'completed', 'delayed', 'dateFrom', 'dateTo'));
    }

    public function financial(Request $request)
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->subYear()->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay();

        $revenue = Payment::select(DB::raw("DATE_FORMAT(paid_at, '%b %Y') as month"), DB::raw('SUM(amount) as total'))
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(paid_at)'))
            ->get();

        return view('reports.revenue', compact('revenue', 'dateFrom', 'dateTo'));
    }

    public function medical(Request $request)
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay();

        $abnormalCount = TestResult::whereIn('flag', ['High', 'Low'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $topAbnormal = TestResult::select('tests.name', DB::raw('COUNT(*) as total'))
            ->join('lab_request_items', 'test_results.request_item_id', '=', 'lab_request_items.id')
            ->join('tests', 'lab_request_items.test_id', '=', 'tests.id')
            ->whereIn('test_results.flag', ['High', 'Low'])
            ->whereBetween('test_results.created_at', [$dateFrom, $dateTo])
            ->groupBy('tests.id', 'tests.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('reports.medical', compact('abnormalCount', 'topAbnormal', 'dateFrom', 'dateTo'));
    }

    public function generatePdf(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test', 'items.result', 'items.test.category']);

        $pdf = Pdf::loadView('reports.pdf', compact('labRequest'));

        return $pdf->stream('Report-' . $labRequest->id . '.pdf');
    }

    public function receipt(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test', 'payment']);
        return view('reports.receipt', compact('labRequest'));
    }

    public function thermalReceipt(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test', 'payment']);
        return view('reports.thermal_receipt', compact('labRequest'));
    }
}
