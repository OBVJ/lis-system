<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ __('app.payment_receipt') ?? 'Payment Receipt' }} - RCP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @font-face {
            font-family: 'ArabicFont';
            src: local('Arial Unicode MS'), local('Microsoft Uighur'), local('Tahoma'), local('DejaVu Sans'), local('Noto Sans Arabic'), local('Arial');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'ArabicFontBold';
            src: local('Arial Unicode MS'), local('Microsoft Uighur'), local('Tahoma Bold'), local('DejaVu Sans Bold'), local('Noto Sans Arabic Bold'), local('Arial Bold');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            color: #2d3748;
            line-height: 1.4;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 12px;
            max-width: 80mm;
            margin: 0 auto;
            unicode-bidi: embed;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #009ef7;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-family: 'ArabicFontBold', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            color: #1a202c;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #4a5568;
        }

        .receipt-details,
        .tests-section,
        .total-section,
        .status-section,
        .footer {
            width: 100%;
            box-sizing: border-box;
        }

        .detail-row,
        .test-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .detail-label {
            font-weight: bold;
            color: #2d3748;
        }

        .detail-value {
            min-width: 100px;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .test-name {
            flex: 1;
            font-weight: bold;
        }

        .test-price {
            width: 90px;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .separator {
            border-top: 1px dashed #cbd5e0;
            margin: 10px 0;
        }

        .total-section {
            padding-top: 10px;
            border-top: 2px solid #009ef7;
            margin-top: 10px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .amount-row strong {
            color: #1a202c;
        }

        .amount-value {
            min-width: 100px;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #009ef7;
            margin-top: 6px;
        }

        .status-section {
            padding: 10px;
            border-radius: 4px;
            margin-top: 12px;
            background-color: #edf2f7;
            color: #2d3748;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .status-paid {
            background-color: #e6fffa;
            color: #096b58;
        }

        .status-partial {
            background-color: #fff7e6;
            color: #975a16;
        }

        .status-unpaid,
        .status-pending {
            background-color: #fed7d7;
            color: #9b2c2c;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e0;
            text-align: center;
            font-size: 10px;
            color: #718096;
        }

        .arabic-text {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
        }

        @media print {
            body {
                padding: 10px;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.laboratory_name') ?? 'PREMIUM LAB LIS' }}</h1>
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.payment_receipt') ?? 'Payment Receipt' }}</p>
        <p>{{ __('app.receipt_no') ?? 'Receipt No' }}: RCP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="receipt-details">
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.date') ?? 'Date' }}:</span>
            <span class="detail-value">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.request_id') ?? 'Request ID' }}:</span>
            <span class="detail-value">REQ-{{ str_pad($payment->request_id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.patient') ?? 'Patient' }}:</span>
            <span class="detail-value {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $payment->request->patient->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.patient_code') ?? 'Patient Code' }}:</span>
            <span class="detail-value">{{ $payment->request->patient->patient_code }}</span>
        </div>
    </div>

    <div class="separator"></div>

    <div class="tests-section">
        <div class="detail-row" style="font-weight: bold; margin-bottom: 8px;">
            <span class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.requested_tests') ?? 'Requested Tests' }}</span>
            <span></span>
        </div>
        @foreach($payment->request->items as $item)
            <div class="test-item">
                <span class="test-name {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $item->test->name }}</span>
                <span class="test-price">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($item->test->price, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="separator"></div>

    <div class="total-section">
        <div class="amount-row">
            <span>{{ __('app.subtotal') ?? 'Subtotal' }}:</span>
            <span class="amount-value">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->amount + ($payment->discount_value ?? 0), 2) }}</span>
        </div>
        @if(!empty($payment->discount_value))
            <div class="amount-row">
                <span>{{ __('app.discount') ?? 'Discount' }}:</span>
                <span class="amount-value">- {{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->discount_value, 2) }}</span>
            </div>
        @endif
        <div class="amount-row">
            <span>{{ __('app.paid_amount') ?? 'Paid Amount' }}:</span>
            <span class="amount-value">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->paid_amount ?? 0, 2) }}</span>
        </div>
        <div class="amount-row total-amount">
            <span>{{ __('app.total_amount') ?? 'Total Amount' }}:</span>
            <span class="amount-value">{{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($payment->amount, 2) }}</span>
        </div>
    </div>

    <div class="status-section {{ $payment->status == 'paid' ? 'status-paid' : ($payment->status == 'partial' ? 'status-partial' : ($payment->status == 'refunded' ? 'status-paid' : 'status-unpaid')) }}">
        @if($payment->status == 'paid')
            {{ __('app.payment_received') ?? 'Payment Received' }}
        @elseif($payment->status == 'partial')
            {{ __('app.partial_payment') ?? 'Partial Payment' }}
        @elseif($payment->status == 'refunded')
            {{ __('app.payment_refunded') ?? 'Payment Refunded' }}
        @else
            {{ __('app.payment_pending') ?? 'Payment Pending' }}
        @endif
    </div>

    <div class="footer">
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.thank_you_message') ?? 'Thank you for choosing our services!' }}</p>
        <p>{{ __('app.generated_on') ?? 'Generated on' }}: {{ now()->format('Y-m-d H:i') }}</p>
        <p>{{ __('app.receipt_footer') ?? 'This is a system generated receipt.' }}</p>
    </div>
</body>
</html>
