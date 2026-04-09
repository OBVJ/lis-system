<?php

namespace App\Http\Controllers;

use App\Models\LabRequestItem;
use App\Models\TestResult;
use App\Models\LabRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestResultController extends Controller
{
    public function entry($itemId)
    {
        $item = LabRequestItem::with('test', 'request.patient')->findOrFail($itemId);
        return view('results.entry', compact('item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_item_id' => 'required|exists:lab_request_items,id',
            'result_value' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $item = LabRequestItem::with('test', 'request')->findOrFail($request->request_item_id);

        // Auto-flag logic
        $val = $request->result_value;
        $flag = 'normal';
        if ($item->test->normal_max && $val > $item->test->normal_max) $flag = 'high';
        elseif ($item->test->normal_min && $val < $item->test->normal_min) $flag = 'low';

        TestResult::updateOrCreate(
            ['request_item_id' => $item->id],
            [
                'result_value' => $val,
                'flag' => $flag,
                'notes' => $request->notes,
            ]
        );

        $item->update(['status' => 'completed']);

        // Check if all items in request are completed
        $req = $item->request;
        $allCompleted = $req->items()->where('status', 'pending')->count() == 0;

        if ($allCompleted) {
            $req->update([
                'status' => 'ready',
                'review_at' => now(),
                'review_by' => Auth::id()
            ]);
        } else {
            // If some results are entered but not all, set to in_progress
            if ($req->status !== 'in_progress') {
                $req->update([
                    'status' => 'in_progress',
                    'in_progress_at' => now(),
                    'in_progress_by' => Auth::id()
                ]);
            }
        }

        return redirect()->route('requests.show', $req->id)->with('success', 'Test result saved (Flag: '.strtoupper($flag).').');
    }

    public function bulkEntry(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test.category', 'items.result']);
        return view('results.edit', ['request' => $labRequest]);
    }

    public function bulkStore(Request $request, LabRequest $labRequest)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.item_id' => 'nullable|exists:lab_request_items,id',
            'results.*.result_value' => 'nullable|string',
            'results.*.value' => 'nullable|string',
            'results.*.notes' => 'nullable|string'
        ]);

        foreach ($request->results as $itemKey => $resultData) {
            $itemId = $resultData['item_id'] ?? $itemKey;
            $value = $resultData['result_value'] ?? $resultData['value'] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $item = LabRequestItem::with('test')->findOrFail($itemId);
            if ($item->request_id !== $labRequest->id) {
                continue;
            }

            $flag = 'normal';
            if (is_numeric($value)) {
                $val = (float)$value;
                if ($item->test->normal_max && $val > $item->test->normal_max) {
                    $flag = 'high';
                } elseif ($item->test->normal_min && $val < $item->test->normal_min) {
                    $flag = 'low';
                }
            }

            TestResult::updateOrCreate(
                ['request_item_id' => $item->id],
                [
                    'result_value' => $value,
                    'flag' => $flag,
                    'notes' => $resultData['notes'] ?? null,
                ]
            );

            $item->update(['status' => 'completed']);
        }

        $labRequest->refresh();
        $allCompleted = $labRequest->items()->where('status', '!=', 'completed')->count() == 0;

        if ($allCompleted) {
            $labRequest->update([
                'status' => 'ready',
                'review_at' => now(),
                'review_by' => Auth::id()
            ]);
        } else {
            // If some results are entered but not all, set to in_progress
            if ($labRequest->status !== 'in_progress') {
                $labRequest->update([
                    'status' => 'in_progress',
                    'in_progress_at' => now(),
                    'in_progress_by' => Auth::id()
                ]);
            }
        }

        return redirect()->route('queue')->with('success', 'All test results saved successfully.');
    }
}
