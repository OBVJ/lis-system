<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SampleController extends Controller
{
    public function index()
    {
        $samples = Sample::with('request.patient', 'request.items.test')->latest()->paginate(15);
        return view('samples.index', compact('samples'));
    }

    public function show(Sample $sample)
    {
        $sample->load('request.patient', 'request.items.test', 'request.items.result');
        return view('samples.show', compact('sample'));
    }

    public function print(Sample $sample)
    {
        $sample->load('request.patient', 'request.items.test', 'request.items.result');
        return view('samples.print', compact('sample'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:lab_requests,id',
            'sample_type' => 'required|exists:specimen_types,name',
        ]);

        // Generate Clinical Barcode (e.g. LAB-RANDOM)
        $barcode = 'LAB-' . strtoupper(Str::random(8));

        Sample::create([
            'request_id' => $request->request_id,
            'sample_type' => $request->sample_type,
            'collected_at' => now(),
            'status' => 'collected',
            'barcode' => $barcode,
            'technician_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'Lab Technician',
        ]);

        // Update laboratory workflow status
        $labRequest = LabRequest::find($request->request_id);
        if ($labRequest->status === 'pending') {
            $labRequest->update(['status' => 'sample_collected']);
        }

        return back()->with('success', 'Sample successfully collected with Barcode: ' . $barcode);
    }
}
