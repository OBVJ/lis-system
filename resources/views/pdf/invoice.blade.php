<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_code }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 40px; color: #333; line-height: 1.6; }
        .header { display: table; width: 100%; border-bottom: 3px solid #667eea; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #1a1c23; font-size: 32px; }
        .invoice-details { margin-top: 30px; font-size: 14px; }
        .invoice-details table { width: 100%; }
        .invoice-details td { padding: 5px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .items-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; padding: 12px; font-size: 14px; }
        .items-table td { padding: 12px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        .total-section { margin-top: 30px; text-align: right; }
        .total-row { display: table; width: 300px; float: right; padding: 5px 0; }
        .total-label { color: #888; font-weight: 600; width: 150px; display: inline-block; }
        .total-value { font-weight: 700; width: 145px; display: inline-block; font-size: 18px; }
        .invoice-footer { position: fixed; bottom: 0; width: 100%; text-align: center; border-top: 1px solid #edf2f7; padding-top: 15px; font-size: 11px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p style="margin: 0; color: #718096;">Laboratory Information System - Billing Dept</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td width="30%"><strong>Invoice No:</strong> #{{ $invoice->invoice_code }}</td>
                <td width="30%"><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</td>
                <td width="40%"><strong>Bill To:</strong> {{ $invoice->request->patient->name_en }}</td>
            </tr>
            <tr>
                <td><strong>Patient Code:</strong> {{ $invoice->request->patient->patient_code }}</td>
                <td><strong>Request Code:</strong> {{ $invoice->request->request_code }}</td>
                <td><strong>Phone:</strong> {{ $invoice->request->patient->phone }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Test Component</th>
                <th>Category</th>
                <th align="right">Amount ({{ App\Models\Setting::get('currency_symbol', 'SDG') }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->request->tests as $rt)
            <tr>
                <td>{{ $rt->test->name_en }}</td>
                <td>{{ $rt->test->category->name_en }}</td>
                <td align="right">{{ number_format($rt->price_at_request, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">{{ app_currency($invoice->total_amount) }}</span>
        </div>
        <div class="total-row" style="color: #667eea; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            <span class="total-label">Grand Total:</span>
            <span class="total-value">{{ app_currency($invoice->total_amount) }}</span>
        </div>
        <p style="clear: both; padding-top: 10px; color: {{ $invoice->status == 'paid' ? '#10b981' : '#f59e0b' }}; font-weight: bold; font-size: 14px;">
            Payment Status: {{ strtoupper($invoice->status) }}
        </p>
    </div>

    <div class="invoice-footer">
        <p>Thank you for choosing our laboratory. For any queries, please call +123 456 7890.</p>
        <p>Digital Invoice #{{ $invoice->invoice_code }} generated on {{ now() }}</p>
    </div>
</body>
</html>
