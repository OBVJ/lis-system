<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\InventoryUnit;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryUnitController extends Controller
{
    public function index()
    {
        $units = InventoryUnit::orderBy('name')->get();
        // Manually counting items that use this text-based unit to safely allow deletion
        foreach ($units as $unit) {
            $unit->items_count = InventoryItem::where('unit', $unit->name)->count();
        }
        return view('settings.inventory_units', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:inventory_units,name']);
        
        InventoryUnit::create(['name' => $request->name]);
        
        return back()->with('success', 'Inventory Unit created successfully.');
    }

    public function destroy(InventoryUnit $unit)
    {
        $inUse = InventoryItem::where('unit', $unit->name)->exists();
        if ($inUse) {
            return back()->with('error', 'Cannot delete unit because it is currently used by inventory items.');
        }
        
        $unit->delete();
        
        return back()->with('success', 'Inventory Unit deleted successfully.');
    }
}
