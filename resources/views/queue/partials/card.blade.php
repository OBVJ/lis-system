<div class="card card-lis queue-card mb-2 border shadow-sm rounded-3">
    @php $latestRequest = $patient->requests->first(); @endphp
    <div class="card-body p-3">
        <h6 class="fw-bold mb-1 {{ app()->getLocale() == 'ar' ? 'text-end' : '' }} text-truncate" title="{{ $patient->name }}">
            {{ $patient->name }}
        </h6>
        <p class="text-muted small mb-1 {{ app()->getLocale() == 'ar' ? 'text-end' : '' }}">
            {{ $patient->patient_code }}
        </p>
        @if($patient->request_count > 1)
            <p class="small mb-3 {{ app()->getLocale() == 'ar' ? 'text-end' : '' }}">
                <span class="text-dark">{{ $patient->request_count }} {{ __('app.requests') ?? 'requests' }}</span>
            </p>
        @endif
        <div class="d-grid gap-2">
            @if($status === 'waiting')
                <button type="button" class="btn btn-info btn-sm rounded-pill collect-sample-btn" 
                        data-bs-toggle="modal" data-bs-target="#queueCollectSampleModal"
                        data-request-id="{{ $latestRequest->id ?? '' }}"
                        data-patient-name="{{ $patient->name }}">
                    <i class="fas fa-vial me-1"></i>{{ __('app.collect_sample') ?? 'Collect Sample' }}
                </button>
            @elseif($status === 'sample_collected')
                <a href="{{ route('results.bulk.entry', $latestRequest->id ?? '') }}" 
                   class="btn btn-warning btn-sm rounded-pill">
                    <i class="fas fa-flask me-1"></i>{{ __('app.bulk_results_entry') ?? 'Bulk Results Entry' }}
                </a>
            @elseif($status === 'in_progress')
                <a href="{{ route('results.bulk.entry', $latestRequest->id ?? '') }}" 
                   class="btn btn-warning btn-sm rounded-pill">
                    <i class="fas fa-edit me-1"></i>{{ __('app.continue_results') ?? 'Continue Results' }}
                </a>
            @elseif($status === 'ready')
                <a href="{{ route('reports.pdf', $latestRequest->id ?? '') }}" 
                   target="_blank" class="btn btn-success btn-sm rounded-pill">
                    <i class="fas fa-print me-1"></i>{{ __('app.print_report') ?? 'Print Report' }}
                </a>
            @elseif($status === 'printed_delivered')
                <a href="{{ route('requests.show', $latestRequest->id ?? '') }}" 
                   class="btn btn-dark btn-sm rounded-pill">
                    <i class="fas fa-box-open me-1"></i>{{ __('app.view_details') ?? 'View Details' }}
                </a>
            @endif
            @if($latestRequest && $latestRequest->payment && $latestRequest->payment->status === 'paid')
                <a href="{{ route('patients.receipt', $latestRequest->id) }}" 
                   target="_blank" class="btn btn-outline-success btn-sm rounded-pill">
                    <i class="fas fa-receipt me-1"></i>{{ __('Print Receipt') }}
                </a>
            @endif
            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fas fa-eye me-1"></i>{{ __('app.view_profile') ?? 'View Profile' }}
            </a>
        </div>
    </div>
</div>
