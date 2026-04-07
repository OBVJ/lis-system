@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-heartbeat me-2 text-danger"></i>{{ __('app.medical_insights') ?? 'Medical Insights' }}</h2>
        <p class="text-muted mb-1">{{ __('app.medical_insights_description') ?? 'Review abnormal result patterns and clinical risk signals across recent lab activity.' }}</p>
        <small class="text-muted">{{ optional($dateFrom)->format('M d, Y') }} — {{ optional($dateTo)->format('M d, Y') }}</small>
    </div>
    <div class="col-md-4">
        <form action="{{ route('reports.medical') }}" method="GET" class="row g-2 justify-content-end">
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
        <div class="card p-3 shadow-sm border-top border-danger border-4 rounded-4">
            <h6 class="text-uppercase text-muted small mb-2">{{ __('app.abnormal_results') ?? 'Abnormal Results' }}</h6>
            <h3 class="fw-bold">{{ $abnormalCount }}</h3>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-3 shadow-sm rounded-4">
            <h6 class="text-uppercase text-muted small mb-3">{{ __('app.top_abnormal_tests') ?? 'Top Abnormal Tests' }}</h6>
            <ul class="list-group list-group-flush">
                @forelse($topAbnormal as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $item->name }}</span>
                    <span class="badge bg-danger rounded-pill">{{ $item->total }}</span>
                </li>
                @empty
                <li class="list-group-item text-center text-muted">{{ __('app.no_data') ?? 'No data available' }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection