<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ __('app.invoice') }} - REQ-{{ $payment->request_id }}</title>
    <style>
        @font-face {
            font-family: 'ArabicFont';
            src: local('Arial Unicode MS'),
                 local('Microsoft Uighur'),
                 local('Tahoma'),
                 local('DejaVu Sans'),
                 local('Noto Sans Arabic'),
                 local('Arial');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'ArabicFontBold';
            src: local('Arial Unicode MS'),
                 local('Microsoft Uighur'),
                 local('Tahoma Bold'),
                 local('DejaVu Sans Bold'),
                 local('Noto Sans Arabic Bold'),
                 local('Arial Bold');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
            line-height: 1.6;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
            unicode-bidi: embed;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #009ef7;
            padding-bottom: 20px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .header h1 {
            margin: 0;
            color: #1e1e2d;
            font-size: 32px;
            font-family: 'ArabicFontBold', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #718096;
            font-size: 16px;
        }

        .invoice-details {
            margin-top: 30px;
            font-size: 14px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .invoice-details table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        .invoice-details td {
            padding: 8px 0;
            vertical-align: top;
        }

        .invoice-details strong {
            color: #1e1e2d;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .items-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            padding: 12px;
            font-size: 14px;
            font-weight: bold;
            color: #1e1e2d;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .items-table td:last-child {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .total-section {
            margin-top: 30px;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .total-row {
            display: table;
            width: 300px;
            float: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            padding: 5px 0;
        }

        .total-label {
            color: #888;
            font-weight: 600;
            width: 150px;
            display: inline-block;
        }

        .total-value {
            font-weight: 700;
            width: 145px;
            display: inline-block;
            font-size: 18px;
        }

        .invoice-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #edf2f7;
            padding-top: 15px;
            font-size: 11px;
            color: #a0aec0;
            clear: both;
        }

        .status-paid {
            color: #50cd89;
            font-weight: bold;
        }

        .status-unpaid {
            color: #ffc700;
            font-weight: bold;
        }

        .arabic-text {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
            unicode-bidi: embed;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.laboratory_invoice') }}</h1>
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.laboratory_invoice') }} - {{ __('app.billing_department') }}</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td width="30%" class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.invoice_no') }}:</strong> INV-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                </td>
                <td width="30%" class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.date') }}:</strong> {{ $payment->created_at->format('Y-m-d') }}
                </td>
                <td width="40%" class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.bill_to') }}:</strong> {{ $payment->request->patient->name }}
                </td>
            </tr>
            <tr>
                <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.patient_code') }}:</strong> {{ $payment->request->patient->patient_code }}
                </td>
                <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.request_id') }}:</strong> REQ-{{ $payment->request_id }}
                </td>
                <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.phone') }}:</strong> {{ $payment->request->patient->phone ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <strong>{{ __('app.status') }}:</strong>
                    @if($payment->status == 'paid')
                        <span class="status-paid">{{ __('app.paid') }}</span>
                        @if($payment->paid_at)
                            ({{ __('app.paid_on') }}: {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d') }})
                        @endif
                    @else
                        <span class="status-unpaid">{{ __('app.unpaid') }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.test_name') }}</th>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.category') }}</th>
                <th>{{ __('app.price') }} ({{ \App\Models\Setting::get('currency_symbol', 'SDG') }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment->request->items as $item)
            <tr>
                <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $item->test->name }}</td>
                <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $item->test->category->name ?? 'N/A' }}</td>
                <td>{{ number_format($item->test->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.subtotal') }}:</span>
            <span class="total-value">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->amount, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.total') }}:</span>
            <span class="total-value" style="color: #009ef7; font-size: 24px;">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->amount, 2) }}</span>
        </div>
    </div>

    <div class="invoice-footer">
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.thank_you_message') }}</p>
        <p>{{ __('app.generated_on') }} {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>