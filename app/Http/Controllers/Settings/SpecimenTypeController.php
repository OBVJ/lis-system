<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SpecimenType;
use Illuminate\Http\Request;

class SpecimenTypeController extends Controller
{
    public function index()
    {
        $types = SpecimenType::all();
        return view('settings.specimen_types', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:specimen_types,name']);
        SpecimenType::create($request->all());
        return back()->with('success', 'Specimen type added successfully.');
    }

    public function destroy(SpecimenType $type)
    {
        // Check if in use? 
        $type->delete();
        return back()->with('success', 'Specimen type deleted successfully.');
    }
}
