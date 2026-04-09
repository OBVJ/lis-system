<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.lab_report') ?? 'Lab Report' }} #{{ $labRequest->id }}</title>
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
            padding: 24px;
            color: #2d2d2d;
            font-size: 11px;
            line-height: 1.4;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        .arabic-text {
            font-family: 'ArabicFont', 'DejaVu Sans', 'Arial Unicode MS', 'Tahoma', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
        }

        .header {
            border-bottom: 2px solid #0d6efd;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 0.5px;
            font-family: {{ app()->getLocale() === 'ar' ? "'DejaVu Sans', 'Arial Unicode MS', sans-serif" : "Helvetica, Arial, sans-serif" }};
        }

        .brand-logo svg {
            width: 40px;
            height: 40px;
        }

        .header-right {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 18px 0;
            text-decoration: underline;
            text-align: center;
        }

        .section-box {
            background: #f4f7fb;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 1px solid #e9eef4;
        }

        .section-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .section-box td {
            padding: 6px 0;
            vertical-align: top;
        }

        .section-label {
            font-weight: bold;
            color: #40475d;
            width: 120px;
            white-space: nowrap;
        }

        .section-value {
            color: #222;
            font-weight: 600;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .results-table th,
        .results-table td {
            padding: 10px 12px;
            border: 1px solid #e6ecf5;
            vertical-align: middle;
        }

        .results-table th {
            background: #0d6efd;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
        }

        .results-table tr.high-result {
            background: rgba(220, 53, 69, 0.08);
        }

        .results-table tr.low-result {
            background: rgba(253, 126, 20, 0.08);
        }

        .results-table tr.normal-result {
            background: rgba(25, 135, 84, 0.06);
        }

        .result-value {
            font-weight: bold;
            color: #1b2838;
        }

        .flag-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .flag-high { background: #fde2e6; color: #b02a37; }
        .flag-low { background: #fff4e6; color: #b45309; }
        .flag-normal { background: #e6f4ea; color: #1f7a3e; }

        .qr-box {
            width: 130px;
            height: 130px;
            border: 1px solid #d9e2ef;
            border-radius: 12px;
            padding: 8px;
            background: #fff;
            text-align: center;
        }

        .qr-box img {
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .qr-caption {
            font-size: 10px;
            color: #5f6b7d;
            margin-top: 6px;
        }

        .recommendation {
            background: #fff8e7;
            border-left: 4px solid #f59e0b;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 14px;
        }

        .recommendation h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #92400e;
        }

        .recommendation p,
        .recommendation li {
            margin: 0 0 6px 0;
            color: #5c4126;
            font-size: 12px;
            line-height: 1.45;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            gap: 12px;
        }

        .signature-block {
            width: 220px;
            border-top: 1px solid #333;
            padding-top: 8px;
            text-align: center;
            font-size: 12px;
            color: #333;
            font-weight: 700;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; width: 65%;" class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                    <div style="font-size: 22px; font-weight: bold; color: #0d6efd; font-family: 'ArabicFontBold', sans-serif;">
                        {{ ar_reshape(__('app.premium_lab')) }}
                    </div>
                    <div style="margin-top: 8px; font-size: 12px; color: #5f6b7d;">
                        {{ ar_reshape(__('app.lab_address_line1')) }}<br>
                        {{ ar_reshape(__('app.lab_contact')) }}
                    </div>
                </td>
                <td class="header-right {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}" style="vertical-align: top; width: 35%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                    <div style="font-size: 16px; font-weight: bold; color: #1e1e2d; margin-bottom: 4px;">{{ ar_reshape(__('app.medical_laboratory_report')) }}</div>
                    <div style="font-size: 12px; color: #718096;">{{ ar_reshape(__('app.report_date')) }}: {{ now()->format('d/m/Y H:i') }}</div>
                    <div style="font-size: 12px; color: #718096; margin-top: 6px;">{{ ar_reshape(__('app.report_id')) }}: REQ-{{ str_pad($labRequest->id, 5, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-box {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
        <table>
            <tr>
                <td class="section-label">{{ ar_reshape(__('app.patient_name')) }}:</td>
                <td class="section-value">{{ ar_reshape($labRequest->patient->name) }}</td>
                <td class="section-label">{{ ar_reshape(__('app.patient_id')) }}:</td>
                <td class="section-value">{{ ar_reshape($labRequest->patient->patient_code) }}</td>
            </tr>
            <tr>
                <td class="section-label">{{ ar_reshape(__('app.age')) }}:</td>
                <td class="section-value">{{ $labRequest->patient->age }} {{ ar_reshape(__('app.years')) }}</td>
                <td class="section-label">{{ ar_reshape(__('app.gender')) }}:</td>
                <td class="section-value">{{ ar_reshape(__('app.' . strtolower($labRequest->patient->gender))) }}</td>
            </tr>
            <tr>
                <td class="section-label">{{ ar_reshape(__('app.ref_doctor')) }}:</td>
                <td class="section-value">{{ ar_reshape($labRequest->patient->referring_doctor ?? __('app.self_referral')) }}</td>
                <td class="section-label">{{ ar_reshape(__('app.status')) }}:</td>
                <td class="section-value">{{ ar_reshape(__('app.' . strtolower($labRequest->patient->status)) ?? strtoupper($labRequest->status)) }}</td>
            </tr>
        </table>
    </div>

    <div class="report-title {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.laboratory_investigation_report')) }}</div>

    <table class="results-table {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
        <thead>
            <tr>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.test_name')) }}</th>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.result')) }}</th>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.unit')) }}</th>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.reference_range')) }}</th>
                <th class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.clinical_flag')) }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labRequest->items as $item)
                @php
                    $flag = $item->result->flag ?? 'Pending';
                    $rowClass = strtolower($flag) === 'high' ? 'high-result' : (strtolower($flag) === 'low' ? 'low-result' : 'normal-result');
                @endphp
                <tr class="{{ $item->result ? $rowClass : '' }}">
                    <td class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                        <strong>{{ ar_reshape($item->test->name) }}</strong><br>
                        <small style="color: #6b7280;">{{ ar_reshape($item->test->category->name ?? '') }}</small>
                    </td>
                    <td class="result-value">{{ ar_reshape($item->result->result_value ?? __('app.not_available')) }}</td>
                    <td>{{ ar_reshape($item->test->unit) }}</td>
                    <td>{{ $item->test->normal_min }} - {{ $item->test->normal_max }}</td>
                    <td>
                        <span class="flag-badge {{ strtolower($flag) === 'high' ? 'flag-high' : (strtolower($flag) === 'low' ? 'flag-low' : 'flag-normal') }} {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
                            {{ ar_reshape(__('app.' . strtolower($flag)) ?? strtoupper($flag)) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $abnormals = $labRequest->items->filter(function ($item) {
            return $item->result && in_array(strtolower($item->result->flag), ['high', 'low']);
        });
    @endphp

    @if($abnormals->count() > 0)
        <div class="recommendation {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
            <h4 class="{{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">{{ ar_reshape(__('app.clinical_suggestions')) }}</h4>
            <ul>
                @foreach($abnormals as $ab)
                    <li>
                        <strong>{{ ar_reshape($ab->test->name) }} ({{ ar_reshape(__('app.' . strtolower($ab->result->flag))) }}):</strong>
                        {{ ar_reshape(__('app.clinical_correlation_recommended')) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="signature-area">
        <div class="qr-box">
            @if(!empty($qrImage))
                <img src="{{ $qrImage }}" alt="{{ __('app.qr_code') ?? 'QR Code' }}">
            @else
                <div style="font-size: 10px; color: #6b7280; line-height: 1.4;">{{ __('app.qr_unavailable') ?? 'QR code unavailable' }}</div>
            @endif
            <div class="qr-caption">{{ ar_reshape(__('app.scan_for_report') ?? 'Scan for report verification') }}</div>
        </div>

        <div class="signature-block">
            {{ ar_reshape(__('app.lab_director_signature') ?? 'Lab Director Signature') }}
        </div>
    </div>

    <div class="footer {{ app()->getLocale() === 'ar' ? 'arabic-text' : '' }}">
        {{ ar_reshape(__('app.confidential_medical_record')) }} | {{ ar_reshape(__('app.generated_by')) }} LIS
    </div>
</body>
</html>
