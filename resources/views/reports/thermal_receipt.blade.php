<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $labRequest->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 5px;
            width: 80mm;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .double-line { border-bottom: 2px solid #000; margin: 5px 0; }
        .receipt-header { margin-bottom: 10px; }
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-info {
            font-size: 10px;
            margin-bottom: 10px;
        }
        .patient-info {
            margin-bottom: 10px;
        }
        .tests-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .tests-table th,
        .tests-table td {
            text-align: left;
            padding: 2px 0;
            font-size: 11px;
        }
        .total-section {
            margin-top: 10px;
        }
        .footer {
            margin-top: 15px;
            font-size: 10px;
        }
        @media print {
            body { width: auto; }
        }
    </style>
</head>
<body>
    <div class="receipt-header center">
        <div class="receipt-title">{{ __('app.lab_name') ?? 'PREMIUM MEDICAL LAB' }}</div>
        <div class="receipt-info">
            {{ __('app.lab_address') ?? '123 Medical Plaza, Health Street' }}<br>
            {{ __('app.lab_phone') ?? 'Phone: +249 123 456 789' }}<br>
            {{ __('app.lab_email') ?? 'Email: info@premium-lab.com' }}
        </div>
    </div>

    <div class="line"></div>

    <div class="center bold">{{ __('app.receipt') ?? 'RECEIPT' }}</div>
    <div class="center">{{ __('app.receipt_no') ?? 'Receipt No:' }} #{{ str_pad($labRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="center">{{ __('app.date') ?? 'Date:' }} {{ $labRequest->created_at->format('d/m/Y H:i') }}</div>

    <div class="line"></div>

    <div class="patient-info">
        <strong>{{ __('app.patient') ?? 'Patient:' }}</strong> {{ $labRequest->patient->name }}<br>
        <strong>{{ __('app.patient_code') ?? 'Code:' }}</strong> {{ $labRequest->patient->patient_code }}<br>
        <strong>{{ __('app.age') ?? 'Age:' }}</strong> {{ $labRequest->patient->age }} {{ __('app.years') ?? 'years' }}<br>
        <strong>{{ __('app.phone') ?? 'Phone:' }}</strong> {{ $labRequest->patient->phone ?? __('app.not_provided') }}
    </div>

    <div class="line"></div>

    <table class="tests-table">
        <thead>
            <tr>
                <th>{{ __('app.test') ?? 'Test' }}</th>
                <th style="text-align: right;">{{ __('app.price') ?? 'Price' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labRequest->items as $item)
            <tr>
                <td>{{ $item->test->name }}</td>
                <td style="text-align: right;">{{ number_format($item->test->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <div class="total-section">
        <div style="display: flex; justify-content: space-between;">
            <span>{{ __('app.subtotal') ?? 'Subtotal:' }}</span>
            <span>{{ number_format($labRequest->total_price + ($labRequest->payment->discount_value ?? 0), 2) }}</span>
        </div>

        @if($labRequest->payment && $labRequest->payment->discount_value > 0)
        <div style="display: flex; justify-content: space-between;">
            <span>{{ __('app.discount') ?? 'Discount:' }}</span>
            <span>-{{ number_format($labRequest->payment->discount_value, 2) }}</span>
        </div>
        @endif

        <div class="double-line"></div>

        <div style="display: flex; justify-content: space-between; font-weight: bold;">
            <span>{{ __('app.total') ?? 'TOTAL:' }}</span>
            <span>{{ number_format($labRequest->total_price, 2) }}</span>
        </div>

        <div style="display: flex; justify-content: space-between;">
            <span>{{ __('app.paid') ?? 'Paid:' }}</span>
            <span>{{ number_format($labRequest->payment->paid_amount ?? 0, 2) }}</span>
        </div>

        @php
            $remaining = $labRequest->total_price - ($labRequest->payment->paid_amount ?? 0);
        @endphp

        @if($remaining > 0)
        <div style="display: flex; justify-content: space-between;">
            <span>{{ __('app.remaining') ?? 'Remaining:' }}</span>
            <span>{{ number_format($remaining, 2) }}</span>
        </div>
        @endif
    </div>

    <div class="line"></div>

    <div class="center footer">
        {{ __('app.thank_you') ?? 'Thank you for choosing our services!' }}<br>
        {{ __('app.receipt_footer') ?? 'This is a computer generated receipt' }}
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>