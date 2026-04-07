<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\LabRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('request.patient');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $cleanId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($sub) use ($search, $cleanId) {
                if ($cleanId !== '') {
                    $sub->orWhere('request_id', $cleanId);
                }
                $sub->orWhereHas('request.patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $summary = Payment::query();
        $revenueToday = (clone $summary)->where('status', 'paid')->whereDate('paid_at', now())->sum('paid_amount');
        $revenueThisMonth = (clone $summary)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('paid_amount');
        $refundTotal = (clone $summary)->where('refund_amount', '>', 0)->sum('refund_amount');

        $totalPaid = (clone $query)->where('status', 'paid')->sum('paid_amount');
        $totalUnpaid = (clone $query)->where('status', 'unpaid')->sum('amount');
        $payments = $query->latest()->paginate(15);

        return view('billing.index', compact('payments', 'totalPaid', 'totalUnpaid', 'revenueToday', 'revenueThisMonth', 'refundTotal'));
    }

    public function markPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_amount' => $payment->amount,
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payment marked as paid.');
    }

    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'refund_note' => 'nullable|string|max:500',
        ]);

        if ($payment->refund_amount > 0 || $payment->status === 'refunded') {
            return redirect()->back()->with('error', 'Refund already processed for this payment.');
        }

        if ($payment->paid_amount <= 0) {
            return redirect()->back()->with('error', 'Cannot refund payment that has no paid amount.');
        }

        $payment->update([
            'refund_amount' => $payment->paid_amount,
            'refunded_at' => now(),
            'refund_note' => $request->refund_note,
            'status' => 'refunded',
        ]);

        return redirect()->back()->with('success', 'Payment refunded successfully.');
    }

    public function invoice(Payment $payment)
    {
        $payment->load('request.patient', 'request.items.test');

        $pdf = Pdf::loadView('billing.invoice_pdf', compact('payment'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function receipt(Payment $payment)
    {
        $payment->load('request.patient', 'request.items.test');

        $pdf = Pdf::loadView('billing.receipt', compact('payment'))
            ->setPaper([0, 0, 226.77, 500], 'portrait'); // 80mm width

        return $pdf->download('Receipt_' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
