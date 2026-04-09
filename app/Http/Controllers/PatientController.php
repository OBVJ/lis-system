<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('patient_code', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $patients = $query->latest()->paginate(10);
        return view('patients.index', compact('patients'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = trim($request->input('q'));

        $query = Patient::query();
        if ($search !== '') {
            $query->where('name', 'like', "%$search%")
                ->orWhere('patient_code', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        }

        $patients = $query->latest()->limit(10)->get(['id', 'name', 'patient_code', 'age', 'gender', 'phone', 'address', 'patient_type']);

        return response()->json($patients);
    }

    public function create()
    {
        $tests = \App\Models\LabTest::with('category')->get();
        return view('patients.create', compact('tests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'patient_type' => 'nullable|string',
            'treating_doctor' => 'nullable|string|max:255',
            'referring_doctor' => 'nullable|string|max:255',
            'test_ids' => 'nullable|array',
            'test_ids.*' => 'exists:tests,id',
            'priority' => 'nullable|in:normal,urgent',
            'notes' => 'nullable|string',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $patient = DB::transaction(function () use ($validated, $request) {
            if (!empty($validated['patient_id'])) {
                $patient = Patient::findOrFail($validated['patient_id']);
                $patient->update(collect($validated)->except(['patient_id', 'test_ids', 'priority', 'notes', 'discount_type', 'discount_value', 'paid_amount'])->toArray());
            } else {
                // Auto-generate Unique Patient Code: PT-YYYY-NNNN
                $year = now()->year;
                $count = Patient::whereYear('created_at', $year)->count() + 1;

                do {
                    $code = 'PT-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                    $exists = Patient::where('patient_code', $code)->exists();
                    if ($exists) {
                        $count++;
                    }
                } while ($exists);

                $validated['patient_code'] = $code;
                $patient = Patient::create(collect($validated)->except(['patient_id', 'test_ids', 'priority', 'notes', 'discount_type', 'discount_value', 'paid_amount'])->toArray());
            }

            $labRequestId = null;

            if (!empty($request->test_ids)) {
                $totalPrice = \App\Models\LabTest::whereIn('id', $request->test_ids)->sum('price');

                // Calculate discount
                $discount = 0;
                if ($request->discount_type && $request->discount_value > 0) {
                    if ($request->discount_type === 'percentage') {
                        $discount = ($totalPrice * $request->discount_value) / 100;
                    } else {
                        $discount = min($request->discount_value, $totalPrice);
                    }
                }
                $finalTotal = $totalPrice - $discount;

                $labRequest = \App\Models\LabRequest::create([
                    'patient_id' => $patient->id,
                    'status' => 'waiting',
                    'total_price' => $finalTotal,
                    'priority' => $request->priority ?? 'normal',
                    'notes' => $request->notes,
                    'created_by' => Auth::id()
                ]);

                $labRequestId = $labRequest->id;

                foreach ($request->test_ids as $testId) {
                    \App\Models\LabRequestItem::create([
                        'request_id' => $labRequest->id,
                        'test_id' => $testId,
                        'status' => 'pending',
                    ]);
                }

                // Determine payment status
                $paidAmount = $request->paid_amount ?? 0;
                $paymentStatus = 'unpaid';
                if ($paidAmount >= $finalTotal && $finalTotal > 0) {
                    $paymentStatus = 'paid';
                } elseif ($paidAmount > 0) {
                    $paymentStatus = 'partial';
                }

                \App\Models\Payment::create([
                    'request_id' => $labRequest->id,
                    'amount' => $finalTotal,
                    'status' => $paymentStatus,
                    'paid_amount' => min($paidAmount, $finalTotal),
                    'discount_type' => $request->discount_type,
                    'discount_value' => $discount,
                    'paid_at' => $paymentStatus === 'paid' ? now() : null,
                ]);
            }
            
            return ['patient' => $patient, 'lab_request_id' => $labRequestId, 'payment_status' => $paymentStatus ?? 'unpaid'];
        });

        if (!empty($request->test_ids)) {
            if ($patient['payment_status'] === 'paid') {
                return redirect()->route('patients.receipt', $patient['lab_request_id'])
                                 ->with('success', 'Patient registered, tests selected, and payment processed successfully.');
            }
            return redirect()->route('queue')->with('success', 'Patient registered, tests selected, and payment processed successfully.')->with('new_request_id', $patient['lab_request_id']);
        }

        return redirect()->route('requests.create', ['patient_id' => $patient['patient']->id])
                         ->with('success', 'Patient registered. You can now add tests.');
    }

    public function show(Patient $patient)
    {
        $patient->load('requests.items.test', 'requests.items.result');
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'patient_type' => 'nullable|string',
            'treating_doctor' => 'nullable|string|max:255',
            'referring_doctor' => 'nullable|string|max:255',
        ]);

        $patient->update($validated);
        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function printReceipt($requestId)
    {
        $labRequest = \App\Models\LabRequest::with('patient', 'items.test', 'payment')->findOrFail($requestId);

        return view('patients.receipt', compact('labRequest'));
    }
}
