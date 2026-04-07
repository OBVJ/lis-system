<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\LabTest;
use App\Models\RequestTest;
use App\Models\LabSample;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $requests = LabRequest::with(['patient', 'tests.test'])->latest()->paginate(10);
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $patients = Patient::all();
        $tests = LabTest::all();
        return view('requests.create', compact('patients', 'tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_ids' => 'required|array',
            'test_ids.*' => 'exists:lab_tests,id',
        ]);

        return DB::transaction(function() use ($request) {
            $request_code = 'REQ-' . date('Ymd') . '-' . str_pad(LabRequest::whereDate('created_at', date('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT);

            $lab_request = LabRequest::create([
                'patient_id' => $request->patient_id,
                'request_code' => $request_code,
                'doctor_name' => $request->doctor_name,
                'status' => 'pending',
            ]);

            $total_price = 0;
            foreach ($request->test_ids as $test_id) {
                $test = LabTest::find($test_id);
                RequestTest::create([
                    'lab_request_id' => $lab_request->id,
                    'lab_test_id' => $test_id,
                    'price_at_request' => $test->price,
                ]);
                $total_price += $test->price;
            }

            $lab_request->update(['total_price' => $total_price]);

            // Generate Sample
            LabSample::create([
                'lab_request_id' => $lab_request->id,
                'sample_code' => 'SMP-' . date('Ymd') . '-' . str_pad(LabSample::whereDate('created_at', date('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT),
            ]);

            // Generate Invoice
            Invoice::create([
                'lab_request_id' => $lab_request->id,
                'invoice_code' => 'INV-' . date('Ymd') . '-' . str_pad(Invoice::whereDate('created_at', date('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT),
                'total_amount' => $total_price,
                'status' => 'unpaid',
            ]);

            return redirect()->route('requests.index')->with('success', 'Request created successfully');
        });
    }

    public function show(LabRequest $request)
    {
        $request->load(['patient', 'tests.test', 'sample', 'invoice']);
        return view('requests.show', compact('request'));
    }
}
