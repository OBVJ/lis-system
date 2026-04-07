<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::orderBy('name')->get();
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        $categories = InventoryCategory::orderBy('name')->get();
        $units = InventoryUnit::orderBy('name')->get();
        return view('inventory.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'min_level' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $validated['current_stock'] = 0; // Starts at 0, must be explicitly stocked in

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Item created successfully.');
    }

    public function edit(InventoryItem $inventory)
    {
        $categories = InventoryCategory::orderBy('name')->get();
        $units = InventoryUnit::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'categories', 'units'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'min_level' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        if ($inventory->current_stock > 0) {
            return redirect()->back()->with('error', 'Cannot delete item with active stock. Stock out thoroughly first.');
        }

        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Item deleted successfully.');
    }

    public function transaction(InventoryItem $inventory)
    {
        return view('inventory.transaction', compact('inventory'));
    }

    public function storeTransaction(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'out' && $inventory->current_stock < $validated['quantity']) {
            return redirect()->back()->withInput()->with('error', 'Insufficient stock for this transaction.');
        }

        // Apply transaction
        InventoryTransaction::create([
            'inventory_item_id' => $inventory->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'remarks' => $validated['remarks'],
            'user_id' => Auth::id()
        ]);

        // Update current stock
        if ($validated['type'] === 'in') {
            $inventory->increment('current_stock', $validated['quantity']);
        } else {
            $inventory->decrement('current_stock', $validated['quantity']);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock transaction recorded successfully.');
    }
}
