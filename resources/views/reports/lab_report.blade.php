<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 0; padding: 0; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2980b9; margin-bottom: 5px; text-transform: uppercase; }
        .header p { color: #555; margin: 2px 0; font-size: 11px; }
        .patient-info { width: 100%; border-collapse: collapse; margin-bottom: 30px; background-color: #f8f9fa; }
        .patient-info td { padding: 8px 12px; border: 1px solid #dee2e6; }
        .label { font-weight: bold; color: #34495e; width: 15%; }
        .section-title { font-size: 18px; font-weight: bold; color: #34495e; border-bottom: 2px solid #34495e; margin-bottom: 15px; padding-bottom: 5px; }
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .results-table th { background-color: #34495e; color: #fff; padding: 10px; text-align: left; }
        .results-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .flag { font-weight: bold; padding: 3px 8px; border-radius: 4px; }
        .flag-high { color: #c0392b; background-color: #fadbd8; }
        .flag-low { color: #d35400; background-color: #fdebd0; }
        .flag-normal { color: #27ae60; background-color: #d4efdf; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; font-size: 10px; color: #777; }
        .signature { margin-top: 60px; text-align: right; padding-right: 50px; }
        .signature-line { border-top: 1px solid #000; width: 200px; display: inline-block; }
    </style>
</head>
<body>

<div class="header">
    <h1>APEX MEDICAL CLINIC & LABORATORY</h1>
    <p>123 Medical Center Blvd, Health City, HC 12345</p>
    <p>Phone: (555) 123-4567 | Email: results@apexlab.com</p>
    <p>License: #LAB-9988-CLIA</p>
</div>

<table class="patient-info">
    <tr>
        <td class="label">Patient Name:</td>
        <td style="width: 35%"><strong>{{ $labRequest->patient->name }}</strong></td>
        <td class="label">Request ID:</td>
        <td style="width: 35%">REQ-{{ str_pad($labRequest->id, 5, '0', STR_PAD_LEFT) }}</td>
    </tr>
    <tr>
        <td class="label">Patient Code:</td>
        <td>{{ $labRequest->patient->patient_code }}</td>
        <td class="label">Requested Date:</td>
        <td>{{ $labRequest->created_at->format('M d, Y') }}</td>
    </tr>
    <tr>
        <td class="label">Age / Gender:</td>
        <td>{{ $labRequest->patient->age }} y / {{ ucfirst($labRequest->patient->gender) }}</td>
        <td class="label">Report Date:</td>
        <td>{{ now()->format('M d, Y h:i A') }}</td>
    </tr>
</table>

<div class="section-title">Laboratory Results</div>

<table class="results-table">
    <thead>
        <tr>
            <th>Test Description</th>
            <th>Result Value</th>
            <th>Units</th>
            <th>Reference Range</th>
            <th>Flag</th>
        </tr>
    </thead>
    <tbody>
        @foreach($labRequest->items as $item)
        <tr>
            <td><strong>{{ $item->test->name }}</strong></td>
            <td><strong>{{ $item->result ? $item->result->result_value : 'Pending' }}</strong></td>
            <td>{{ $item->test->unit }}</td>
            <td>{{ $item->test->normal_min }} - {{ $item->test->normal_max }}</td>
            <td>
                @if($item->result)
                    <span class="flag flag-{{ $item->result->flag }}">{{ strtoupper($item->result->flag) }}</span>
                @else
                    ---
                @endif
            </td>
        </tr>
        @if($item->result && $item->result->notes)
        <tr>
            <td colspan="5" style="font-size: 11px; color: #555; padding-top: 2px;">
                <em>Notes: {{ $item->result->notes }}</em>
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>

<div class="signature">
    <div style="margin-bottom: 40px;">Electronically Signed By:</div>
    <div class="signature-line"></div>
    <div style="font-weight: bold; margin-top: 5px;">Dr. Jonathan Doe, MD, PhD</div>
    <div style="font-size: 11px; color: #555;">Laboratory Director</div>
</div>

<div class="footer">
    <p>This is an electronically generated document. Confirmed and verified by the Laboratory Information System.</p>
    <p>Page 1 of 1</p>
</div>

</body>
</html>
