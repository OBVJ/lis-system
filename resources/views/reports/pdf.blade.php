<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Report #{{ $labRequest->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 13px; }
        .header { border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px; }
        .lab-name { font-size: 24px; font-weight: bold; color: #0d6efd; }
        .lab-info { font-size: 11px; color: #666; }
        
        .patient-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .patient-box table { width: 100%; border-collapse: collapse; }
        .patient-box td { padding: 3px 0; }
        .label { font-weight: bold; color: #555; width: 100px; }

        .report-title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 15px; }

        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .results-table th { background: #0d6efd; color: #fff; padding: 10px; text-align: left; }
        .results-table td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .flag-high { color: #dc3545; font-weight: bold; }
        .flag-low { color: #fd7e14; font-weight: bold; }
        .flag-normal { color: #198754; }

        .footer { position: fixed; bottom: 0; width: 100%; font-size: 10px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        .signature-section { margin-top: 50px; }
        .sig-box { width: 200px; border-top: 1px solid #000; text-align: center; float: right; margin-top: 40px; padding-top: 5px; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="lab-name">PREMIUM LAB LIS</div>
                    <div class="lab-info">
                        123 Medical Plaza, Health Street<br>
                        Phone: +1 234 567 890 | Email: office@premium-lab.com
                    </div>
                </td>
                <td style="text-align: right;">
                    <div style="font-weight: bold; font-size: 16px;">MEDICAL LABORATORY REPORT</div>
                    <div>Date: {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="patient-box">
        <table>
            <tr>
                <td class="label">Patient Name:</td>
                <td style="font-size: 15px; font-weight: bold;">{{ strtoupper($labRequest->patient->name) }}</td>
                <td class="label">Patient ID:</td>
                <td>{{ $labRequest->patient->patient_code }}</td>
            </tr>
            <tr>
                <td class="label">Age / Gender:</td>
                <td>{{ $labRequest->patient->age }} Years / {{ ucfirst($labRequest->patient->gender) }}</td>
                <td class="label">Request ID:</td>
                <td>REQ-{{ str_pad($labRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="label">Ref. Doctor:</td>
                <td>{{ $labRequest->patient->referringDoctor->name ?? 'Self' }}</td>
                <td class="label">Report Date:</td>
                <td>{{ now()->format('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="report-title">LABORATORY INVESTIGATION REPORT</div>

    <table class="results-table">
        <thead>
            <tr>
                <th>Investigation (Test)</th>
                <th>Result</th>
                <th>Unit</th>
                <th>Reference Range</th>
                <th>Clinical Flag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labRequest->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->test->name }}</strong><br>
                    <small style="color: #888;">{{ $item->test->category->name }}</small>
                </td>
                <td style="font-size: 14px; font-weight: bold;">{{ $item->result->result_value ?? 'N/A' }}</td>
                <td>{{ $item->test->unit }}</td>
                <td>{{ $item->test->normal_min }} - {{ $item->test->normal_max }}</td>
                <td>
                    @php
                        $flag = $item->result->flag ?? '';
                        $flagClass = '';
                        if ($flag == 'High') $flagClass = 'flag-high';
                        elseif ($flag == 'Low') $flagClass = 'flag-low';
                        elseif ($flag == 'Normal') $flagClass = 'flag-normal';
                    @endphp
                    <span class="{{ $flagClass }}">{{ strtoupper($flag) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $abnormals = $labRequest->items->filter(function($item) {
            return $item->result && in_array($item->result->flag, ['High', 'Low']);
        });
    @endphp

    @if($abnormals->count() > 0)
    <div style="background: #fff8e1; border-left: 5px solid #ffb300; padding: 15px; margin-top: 10px; border-radius: 4px;">
        <h4 style="margin-top: 0; color: #856404; font-size: 14px;"><i class="fas fa-info-circle"></i> Clinical Suggestions & Insights</h4>
        <ul style="margin-bottom: 0; padding-left: 20px; font-size: 12px; color: #664d03;">
            @foreach($abnormals as $ab)
                <li>
                    <strong>{{ $ab->test->name }} ({{ $ab->result->flag }}):</strong> 
                    Clinical correlation recommended. 
                    @if($ab->test->name == 'CBC' || $ab->test->name == 'Hemoglobin')
                        Check for iron deficiency or anemia markers.
                    @elseif($ab->test->name == 'Glucose' || $ab->test->name == 'HbA1c')
                        Fasting blood sugar or OGTT may be required for definitive diagnosis.
                    @else
                        Consider further diagnostic investigations.
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="signature-section">
        <p style="font-size: 11px; color: #666;">Note: This is an electronically generated report and is valid for medical consultation. Please correlate clinically.</p>
        
        <div class="sig-box">
            <strong>Lab Director Signature</strong><br>
            <span style="font-size: 11px; color: #777;">Dr. Clinical Pathologist</span>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="footer">
        Page 1 of 1 | This report was generated by Antigravity LIS | Confidential Medical Record
    </div>
</body>
</html>
