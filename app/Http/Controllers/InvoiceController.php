<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\LabRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['request.patient'])->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['request.patient', 'request.tests.test']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))->setPaper('a4', 'portrait');
        return $pdf->download('invoice-' . $invoice->invoice_code . '.pdf');
    }

    public function downloadReport(LabRequest $request)
    {
        $request->load(['patient', 'tests.test', 'tests.result']);
        $pdf = Pdf::loadView('pdf.report', compact('request'))->setPaper('a4', 'portrait');
        return $pdf->download('report-' . $request->request_code . '.pdf');
    }
}
