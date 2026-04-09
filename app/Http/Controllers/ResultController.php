<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function index()
    {
        // Get all requests that doctors need to work on:
        // 1. Waiting sample collection
        // 2. Samples collected and pending result entry
        // 3. Results entry completed and ready for reporting
        $requests = LabRequest::with(['patient', 'items.test', 'items.result', 'samples'])
            ->whereIn('status', ['waiting', 'sample_collected', 'in_progress', 'ready'])
            ->where(function ($query) {
                $query->whereDoesntHave('samples') // No samples collected yet
                      ->orWhereHas('samples', function ($q) {
                          $q->where('status', 'collected');
                      }) // Samples collected
                      ->orWhereHas('items.result'); // Has results entered
            })
            ->latest()
            ->paginate(15);

        return view('results.index', compact('requests'));
    }

    public function edit(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'items.test', 'items.result', 'samples']);

        if (in_array($labRequest->status, ['waiting', 'pending'])) {
            return redirect()->route('results.index')->with('error', 'Cannot enter results before sample collection.');
        }

        return view('results.edit', ['request' => $labRequest]);
    }

    public function bulkStore(Request $request, LabRequest $labRequest)
    {
        $data = $request->validate([
            'results' => 'required|array',
            'results.*.item_id' => 'required|exists:lab_request_items,id',
            'results.*.value' => 'nullable|string',
            'results.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $labRequest) {
            foreach ($data['results'] as $resultData) {
                if ($resultData['value'] === null || trim($resultData['value']) === '') continue;

                $item = LabRequestItem::with('test')->findOrFail($resultData['item_id']);

                // Auto-Flag Logic for numeric results
                $flag = 'Normal';
                if (is_numeric($resultData['value']) && $item->test->normal_min !== null && $item->test->normal_max !== null) {
                    $val = (float)$resultData['value'];
                    if ($val < $item->test->normal_min) {
                        $flag = 'Low';
                    } elseif ($val > $item->test->normal_max) {
                        $flag = 'High';
                    }
                }

                $isNewResult = TestResult::where('request_item_id', $item->id)->doesntExist();

                TestResult::updateOrCreate(
                    ['request_item_id' => $item->id],
                    [
                        'result_value' => $resultData['value'],
                        'flag' => $flag,
                        'notes' => $resultData['notes'] ?? null,
                    ]
                );

                // Auto-deduct inventory
                if ($isNewResult && $item->test->materials->count() > 0) {
                    foreach ($item->test->materials as $material) {
                        if ($material->current_stock >= $material->pivot->quantity_required) {
                            $material->decrement('current_stock', $material->pivot->quantity_required);
                            
                            // Log transaction
                            \App\Models\InventoryTransaction::create([
                                'inventory_item_id' => $material->id,
                                'type' => 'out',
                                'quantity' => $material->pivot->quantity_required,
                                'notes' => 'Auto-deduction for Lab Request REQ-' . $labRequest->id . ' Item ' . $item->test->name,
                                'user_id' => Auth::id() ?? 1,
                            ]);
                        }
                    }
                }

                $item->update(['status' => 'completed']);
            }

            // Check if all items are completed to update request status
            if ($labRequest->items()->where('status', '!=', 'completed')->count() == 0) {
                $labRequest->update(['status' => 'ready']);
            } else {
                $labRequest->update(['status' => 'in_progress']);
            }
        });

        return redirect()->route('results.index')->with('success', 'Results saved successfully.');
    }
}
