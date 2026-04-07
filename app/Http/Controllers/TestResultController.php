<?php

namespace App\Http\Controllers;

use App\Models\LabRequestItem;
use App\Models\TestResult;
use App\Models\LabRequest;
use Illuminate\Http\Request;

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
        if ($req->items()->where('status', 'pending')->count() == 0) {
            $req->update(['status' => 'review']);
        } else {
            $req->update(['status' => 'in_progress']);
        }

        return redirect()->route('requests.show', $req->id)->with('success', 'Test result saved (Flag: '.strtoupper($flag).').');
    }

    public function bulkEntry(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test.category', 'items.result']);
        return view('results.bulk_entry', compact('labRequest'));
    }

    public function bulkStore(Request $request, LabRequest $labRequest)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.item_id' => 'nullable|exists:lab_request_items,id',
            'results.*.result_value' => 'nullable|numeric',
            'results.*.value' => 'nullable|numeric',
            'results.*.notes' => 'nullable|string'
        ]);

        foreach ($request->results as $itemKey => $resultData) {
            $itemId = $resultData['item_id'] ?? $itemKey;
            $value = $resultData['result_value'] ?? $resultData['value'] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $item = LabRequestItem::with('test')->findOrFail($itemId);
            if ($item->lab_request_id !== $labRequest->id) {
                continue;
            }

            $flag = 'normal';
            if ($item->test->normal_max && $value > $item->test->normal_max) {
                $flag = 'high';
            } elseif ($item->test->normal_min && $value < $item->test->normal_min) {
                $flag = 'low';
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
        if ($labRequest->items()->where('status', '!=', 'completed')->count() == 0) {
            $labRequest->update(['status' => 'completed']);
        } else {
            $labRequest->update(['status' => 'review']);
        }

        return redirect()->route('queue')->with('success', 'All test results saved successfully.');
    }
}
