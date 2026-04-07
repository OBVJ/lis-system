<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LabRequest::with(['patient', 'items.test']);

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'collected') {
                $query->whereIn('status', ['collected', 'sample_collected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search by Request ID or Patient Name
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                // Remove REQ- prefix if present for ID search
                $cleanId = str_replace('REQ-', '', $search);
                $q->where('id', 'like', "%$cleanId%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%");
                  });
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();
        
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $patients = Patient::all();
        $tests = LabTest::with('category')->get();
        return view('requests.create', compact('patients', 'tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:tests,id|distinct',
        ]);

        return DB::transaction(function () use ($request) {
            $totalPrice = LabTest::whereIn('id', $request->test_ids)->sum('price');

            $labRequest = LabRequest::create([
                'patient_id' => $request->patient_id,
                'status' => 'pending',
                'total_price' => $totalPrice,
            ]);

            foreach ($request->test_ids as $testId) {
                LabRequestItem::create([
                    'request_id' => $labRequest->id,
                    'test_id' => $testId,
                    'status' => 'pending',
                ]);
            }

            // Create initial payment record
            Payment::create([
                'request_id' => $labRequest->id,
                'amount' => $totalPrice,
                'status' => 'unpaid',
            ]);

            return redirect()->route('requests.index')->with('success', 'Lab request created successfully.');
        });
    }

    public function show(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test', 'items.result', 'samples', 'payments']);
        return view('requests.show', compact('labRequest'));
    }

    public function updateStatus(Request $request, LabRequest $labRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,collected,sample_collected,in_progress,review,completed,delivered'
        ]);

        $status = $request->status;
        $labRequest->status = $status;

        // Set timestamps and responsible users based on status
        $now = now();
        $userId = auth()->id();

        if ($status === 'collected' || $status === 'sample_collected') {
            $labRequest->collected_at = $now;
            $labRequest->collected_by = $userId;
        } elseif ($status === 'in_progress') {
            $labRequest->in_progress_at = $now;
            $labRequest->in_progress_by = $userId;
        } elseif ($status === 'review') {
            $labRequest->review_at = $now;
            $labRequest->review_by = $userId;
        } elseif ($status === 'completed') {
            // Validate that all test results are entered before completing
            $totalItems = $labRequest->items()->count();
            $completedItems = $labRequest->items()->where('status', 'completed')->count();
            
            if ($totalItems !== $completedItems) {
                return redirect()->back()->with('error', 'Cannot complete request. All test results must be entered first.');
            }
            
            $labRequest->completed_at = $now;
            $labRequest->completed_by = $userId;
        } elseif ($status === 'delivered') {
            // Can only deliver completed requests
            if ($labRequest->status !== 'completed') {
                return redirect()->back()->with('error', 'Cannot deliver request. Must be completed first.');
            }
            
            $labRequest->delivered_at = $now;
            $labRequest->delivered_by = $userId;
        }

        $labRequest->save();

        return redirect()->back()->with('success', 'Lab request status updated successfully to ' . ucfirst(str_replace('_', ' ', $status)));
    }

    public function edit(LabRequest $labRequest)
    {
        $patients = Patient::all();
        $tests = LabTest::with('category')->get();
        return view('requests.edit', compact('labRequest', 'patients', 'tests'));
    }

    public function update(Request $request, LabRequest $labRequest)
    {
        // Simple update logic or restricted based on status
        return redirect()->route('requests.index')->with('success', 'Request updated successfully.');
    }

    public function destroy(LabRequest $labRequest)
    {
        $labRequest->delete();
        return redirect()->route('requests.index')->with('success', 'Lab request deleted successfully.');
    }
}
