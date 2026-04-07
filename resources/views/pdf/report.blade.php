<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Report - {{ $request->request_code }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; margin: 0; padding: 20px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #1a56db; }
        .patient-info { border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #fafafa; margin-bottom: 20px; }
        .patient-info table { width: 100%; border-collapse: collapse; }
        .patient-info td { padding: 5px; font-size: 14px; }
        .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .results-table th { background: #1a56db; color: white; padding: 10px; font-size: 13px; text-transform: uppercase; }
        .results-table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .status-badge { padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: bold; }
        .status-high { color: #ef4444; background: #fee2e2; }
        .status-low { color: #f59e0b; background: #fef3c7; }
        .status-normal { color: #10b981; background: #d1fae5; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px; color: #888; }
        .qr-code { float: right; margin-top: -80px; width: 80px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laboratory Name</h1>
        <p>123 Medical Street, City State, ZIP</p>
        <p>Phone: +123 456 7890 | Email: contact@lab.com</p>
    </div>

    <div class="patient-info">
        <table>
            <tr>
                <td width="50%"><strong>Patient:</strong> {{ $request->patient->name_en }}</td>
                <td width="50%"><strong>Code:</strong> {{ $request->patient->patient_code }}</td>
            </tr>
            <tr>
                <td><strong>DOB:</strong> {{ $request->patient->dob }} ({{ \Carbon\Carbon::parse($request->patient->dob)->age }} yrs)</td>
                <td><strong>Gender:</strong> {{ ucfirst($request->patient->gender) }}</td>
            </tr>
            <tr>
                <td><strong>Request ID:</strong> {{ $request->request_code }}</td>
                <td><strong>Doctor:</strong> {{ $request->doctor_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Date Collected:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td><strong>Date Reported:</strong> {{ now()->format('Y-m-d H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="results-table">
        <thead>
            <tr>
                <th align="left">Investigation</th>
                <th align="center">Result</th>
                <th align="center">Unit</th>
                <th align="center">Ref. Range</th>
                <th align="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->tests as $rt)
            <tr>
                <td>{{ $rt->test->name_en }}</td>
                <td align="center"><strong>{{ $rt->result->result_value ?? 'Pending' }}</strong></td>
                <td align="center">{{ $rt->test->unit }}</td>
                <td align="center">{{ $rt->test->reference_range_en }}</td>
                <td align="center">
                    @if($rt->result)
                        <span class="status-badge status-{{ $rt->result->classification }}">
                            {{ strtoupper($rt->result->classification) }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <p><strong>Note:</strong> Result verified by automated LIS system classification. Please correlate clinically.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Laboratory Information System. Validated Digital Report.</p>
    </div>
</body>
</html>
