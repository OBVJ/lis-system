<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $categories = InventoryCategory::orderBy('name')->get();
        // Manually count active inventory items mapped to this text-based category for safety protection
        foreach ($categories as $cat) {
            $cat->items_count = InventoryItem::where('category', $cat->name)->count();
        }
        return view('settings.inventory_categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:inventory_categories,name']);
        
        InventoryCategory::create(['name' => $request->name]);
        
        return back()->with('success', 'Inventory Category created successfully.');
    }

    public function destroy(InventoryCategory $category)
    {
        $inUse = InventoryItem::where('category', $category->name)->exists();
        if ($inUse) {
            return back()->with('error', 'Cannot delete category because it is actively used by existing inventory items.');
        }
        
        $category->delete();
        
        return back()->with('success', 'Inventory Category deleted successfully.');
    }
}
