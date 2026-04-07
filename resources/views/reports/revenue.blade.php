@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 px-2 flex-column flex-md-row gap-3">
    <div>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>Revenue Summary</h2>
        <p class="text-muted mb-0">{{ __('app.financial_report_description') ?? 'Review revenue performance and collections over custom intervals.' }}</p>
        <small class="text-muted">{{ optional($dateFrom)->format('M d, Y') }} — {{ optional($dateTo)->format('M d, Y') }}</small>
    </div>
    <form action="{{ route('reports.financial') }}" method="GET" class="d-flex gap-2 align-items-end">
        <div class="form-group">
            <label class="form-label small text-muted">{{ __('app.from_date') ?? 'From' }}</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ optional($dateFrom)->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label class="form-label small text-muted">{{ __('app.to_date') ?? 'To' }}</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ optional($dateTo)->format('Y-m-d') }}">
        </div>
        <button type="submit" class="btn btn-lis-primary btn-sm mb-1">{{ __('app.apply') ?? 'Apply' }}</button>
    </form>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card p-3 shadow-lg border-0 border-top border-info border-5">
            <h6 class="text-muted fw-bold small mb-2 text-uppercase">Total YTD Revenue</h6>
            <h2 class="fw-bold text-dark">${{ number_format($revenue->sum('total'), 2) }}</h2>
            <p class="text-success small fw-bold mb-0"><i class="fas fa-arrow-up me-1"></i>+12% from last period</p>
        </div>
    </div>
    <div class="col-md-8 mb-4">
        <div class="card p-4 shadow-lg border-0 rounded-10">
            <h5 class="fw-bold mb-4"><i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Performance</h5>
            <canvas id="revenueChart" style="max-height: 250px;"></canvas>
        </div>
    </div>
</div>

<div class="card p-0 shadow-lg border-0 rounded-10 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light px-4">
            <tr>
                <th class="px-4">Month</th>
                <th class="text-end px-5">Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenue as $row)
            <tr>
                <td class="px-4 fw-bold">{{ $row->month }}</td>
                <td class="text-end px-5 fw-bold text-primary">${{ number_format($row->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    .rounded-10 { border-radius: 20px; }
</style>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! $revenue->pluck('month') !!},
            datasets: [{
                label: 'Revenue ($)',
                data: {!! $revenue->pluck('total') !!},
                backgroundColor: '#3498db',
                borderRadius: 10,
                barThickness: 40
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
