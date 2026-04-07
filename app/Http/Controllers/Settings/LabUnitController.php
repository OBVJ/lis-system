<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LabUnit;
use Illuminate\Http\Request;

class LabUnitController extends Controller
{
    public function index()
    {
        $units = LabUnit::all();
        return view('settings.lab_units', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:lab_units,name']);
        LabUnit::create($request->all());
        return back()->with('success', 'Lab unit added successfully.');
    }

    public function destroy(LabUnit $unit)
    {
        $unit->delete();
        return back()->with('success', 'Lab unit deleted successfully.');
    }
}
