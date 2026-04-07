<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ __('app.sample_label') }} - SMP-{{ str_pad($sample->id, 5, '0', STR_PAD_LEFT) }}</title>
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

        body {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            line-height: 1.4;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 11px;
            unicode-bidi: embed;
            max-width: 80mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #009ef7;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #1e1e2d;
            font-size: 14px;
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', 'Arial', sans-serif;
            font-weight: bold;
        }

        .header p {
            margin: 1px 0;
            color: #718096;
            font-size: 9px;
        }

        .sample-details {
            margin-bottom: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            padding: 1px 0;
        }

        .detail-label {
            font-weight: bold;
            color: #1e1e2d;
            font-size: 10px;
        }

        .detail-value {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 10px;
        }

        .barcode-section {
            text-align: center;
            margin: 8px 0;
            padding: 5px;
            border: 1px solid #009ef7;
            border-radius: 3px;
        }

        .barcode {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #009ef7;
        }

        .tests-section {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            margin-bottom: 10px;
        }

        .test-item {
            margin-bottom: 6px;
            padding: 4px;
            border: 1px solid #f0f0f0;
            border-radius: 2px;
            font-size: 9px;
        }

        .test-name {
            font-weight: bold;
            color: #1e1e2d;
            margin-bottom: 2px;
        }

        .test-result {
            margin-top: 2px;
        }

        .result-value {
            font-weight: bold;
        }

        .status-normal {
            color: #28a745;
            font-weight: bold;
        }

        .status-high {
            color: #dc3545;
            font-weight: bold;
        }

        .status-low {
            color: #ffc107;
            font-weight: bold;
        }

        .status-pending {
            color: #6c757d;
            font-style: italic;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #a0aec0;
            margin-top: 10px;
        }

        .arabic-text {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
            unicode-bidi: embed;
        }

        @media print {
            body {
                padding: 8px;
                max-width: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.laboratory_name') ?? 'PREMIUM LAB LIS' }}</h1>
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.sample_label') }}</p>
        <p>{{ __('app.sample_id') }}: SMP-{{ str_pad($sample->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="sample-details">
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.request_id') }}:</span>
            <span class="detail-value">REQ-{{ str_pad($sample->request->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.patient') }}:</span>
            <span class="detail-value {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $sample->request->patient->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.patient_code') }}:</span>
            <span class="detail-value">{{ $sample->request->patient->patient_code }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.sample_type') }}:</span>
            <span class="detail-value">{{ ucfirst($sample->sample_type) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.collected_at') }}:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($sample->collected_at)->format('M d, Y H:i') }}</span>
        </div>
    </div>

    <div class="barcode-section">
        <div class="barcode">{{ $sample->barcode }}</div>
    </div>

    <div class="tests-section">
        <div style="font-weight: bold; margin-bottom: 5px; font-size: 10px; {{ app()->getLocale() === 'ar' ? 'text-align: right;' : 'text-align: left;' }}">
            {{ __('app.requested_tests') ?? 'Requested Tests' }}:
        </div>
        @foreach($sample->request->items as $item)
        <div class="test-item">
            <div class="test-name {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ $item->test->name }}</div>
            <div class="test-result">
                @if($item->result)
                    <span class="result-value">{{ $item->result->result_value }} {{ $item->test->unit ?? '' }}</span>
                    @if($item->result->flag)
                        <span class="status-{{ strtolower($item->result->flag) }}">
                            ({{ __('app.' . strtolower($item->result->flag)) }})
                        </span>
                    @else
                        <span class="status-normal">({{ __('app.normal') }})</span>
                    @endif
                    @if($item->result->notes)
                        <br><small>{{ __('app.notes') }}: {{ $item->result->notes }}</small>
                    @endif
                @else
                    <span class="status-pending">{{ __('app.pending') }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ __('app.generated_on') }} {{ now()->format('Y-m-d H:i') }}</p>
        <p style="font-size: 7px;">{{ __('app.sample_footer') ?? 'Laboratory Sample Label' }}</p>
    </div>
</body>
</html>

    <a href="#" class="print-button" onclick="window.print(); return false;">{{ __('app.print') }}</a>
</body>
</html>
