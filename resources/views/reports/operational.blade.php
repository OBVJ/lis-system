@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>{{ __('app.operational_reports') ?? 'Operational Reports' }}</h2>
        <p class="text-muted mb-1">{{ __('app.operational_summary_description') ?? 'Track queue, throughput, and delayed requests over the last 30 days.' }}</p>
        <small class="text-muted">{{ optional($dateFrom)->format('M d, Y') }} — {{ optional($dateTo)->format('M d, Y') }}</small>
    </div>
    <div class="col-md-4">
        <form action="{{ route('reports.operational') }}" method="GET" class="row g-2 justify-content-end">
            <div class="col-5">
                <label class="form-label small text-muted">{{ __('app.from_date') ?? 'From' }}</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ optional($dateFrom)->format('Y-m-d') }}">
            </div>
            <div class="col-5">
                <label class="form-label small text-muted">{{ __('app.to_date') ?? 'To' }}</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ optional($dateTo)->format('Y-m-d') }}">
            </div>
            <div class="col-2 d-grid">
                <button type="submit" class="btn btn-lis-primary btn-sm">{{ __('app.apply') ?? 'Apply' }}</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-top border-primary border-4 rounded-4">
            <h6 class="text-uppercase text-muted small mb-2">{{ __('app.pending_requests') }}</h6>
            <h3 class="fw-bold">{{ $pending }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-top border-success border-4 rounded-4">
            <h6 class="text-uppercase text-muted small mb-2">{{ __('app.completed') }}</h6>
            <h3 class="fw-bold">{{ $completed }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-top border-warning border-4 rounded-4">
            <h6 class="text-uppercase text-muted small mb-2">{{ __('app.delayed_requests') }}</h6>
            <h3 class="fw-bold">{{ $delayed }}</h3>
        </div>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">{{ __('app.requests_last_30_days') ?? 'Requests in the Last 30 Days' }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('app.date') ?? 'Date' }}</th>
                        <th>{{ __('app.total_requests') ?? 'Total Requests' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($last30 as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                        <td>{{ $row->count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-4">{{ __('app.no_data') ?? 'No data available' }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection