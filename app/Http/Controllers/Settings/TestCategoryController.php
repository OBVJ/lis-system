<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    public function index()
    {
        $categories = TestCategory::withCount('tests')->orderBy('name')->get();
        return view('settings.test_categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:test_categories,name']);
        
        TestCategory::create(['name' => $request->name]);
        
        return back()->with('success', 'Test Category created successfully.');
    }

    public function destroy(TestCategory $category)
    {
        if ($category->tests()->count() > 0) {
            return back()->with('error', 'Cannot delete category because it contains associated tests.');
        }
        
        $category->delete();
        
        return back()->with('success', 'Test Category deleted successfully.');
    }
}
