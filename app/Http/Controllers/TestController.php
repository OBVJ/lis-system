<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $query = LabTest::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $tests = $query->paginate(15);
        $categories = TestCategory::all();

        return view('tests.index', compact('tests', 'categories'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q', '');
        $tests = LabTest::with('category')
            ->where('name', 'like', "%$search%")
            ->limit(30)
            ->get();

        // Fetch currency ONCE outside the loop to avoid N+1 DB queries
        $currency = \App\Models\Setting::get('currency_symbol', 'SDG');

        $formatted = $tests->map(function ($test) use ($currency) {
            $categoryName = $test->category ? $test->category->name : 'Uncategorized';
            return [
                'id'    => $test->id,
                'text'  => $test->name . ' — ' . number_format($test->price, 2) . ' ' . $currency . ' (' . $categoryName . ')',
                'price' => (float) $test->price,
            ];
        });

        return response()->json($formatted);
    }

    public function create()
    {
        $categories = TestCategory::all();
        $units = \App\Models\LabUnit::all();
        $inventoryItems = \App\Models\InventoryItem::all();
        return view('tests.create', compact('categories', 'units', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:test_categories,id',
            'normal_min' => 'required|numeric',
            'normal_max' => 'required|numeric',
            'unit' => 'required|exists:lab_units,name',
            'price' => 'required|numeric|min:0',
            'materials' => 'nullable|array',
            'materials.*.item_id' => 'required|exists:inventory_items,id',
            'materials.*.quantity' => 'required|numeric|min:0',
        ]);

        $test = LabTest::create($validated);

        if (!empty($validated['materials'])) {
            $materialsData = [];
            foreach ($validated['materials'] as $material) {
                if ($material['item_id'] && $material['quantity']) {
                    $materialsData[$material['item_id']] = ['quantity_required' => $material['quantity']];
                }
            }
            $test->materials()->sync($materialsData);
        }

        return redirect()->route('tests.index')->with('success', 'Test added to catalog.');
    }

    public function edit(LabTest $test)
    {
        $categories = TestCategory::all();
        $units = \App\Models\LabUnit::all();
        $inventoryItems = \App\Models\InventoryItem::all();
        return view('tests.edit', compact('test', 'categories', 'units', 'inventoryItems'));
    }

    public function update(Request $request, LabTest $test)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:test_categories,id',
            'normal_min' => 'required|numeric',
            'normal_max' => 'required|numeric',
            'unit' => 'required|exists:lab_units,name',
            'price' => 'required|numeric|min:0',
            'materials' => 'nullable|array',
            'materials.*.item_id' => 'required|exists:inventory_items,id',
            'materials.*.quantity' => 'required|numeric|min:0',
        ]);

        $test->update($validated);

        $materialsData = [];
        if (!empty($request->materials)) {
            foreach ($request->materials as $material) {
                if ($material['item_id'] && $material['quantity']) {
                    $materialsData[$material['item_id']] = ['quantity_required' => $material['quantity']];
                }
            }
        }
        $test->materials()->sync($materialsData);

        return redirect()->route('tests.index')->with('success', 'Test updated.');
    }

    public function destroy(LabTest $test)
    {
        $test->delete();
        return redirect()->route('tests.index')->with('success', 'Test removed from catalog.');
    }
}
