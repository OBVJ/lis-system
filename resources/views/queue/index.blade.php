@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    @if(session('new_request_id'))
        <a href="{{ route('patients.receipt', session('new_request_id')) }}" target="_blank" class="btn btn-sm btn-outline-success ms-3">
            <i class="fas fa-print me-1"></i>{{ __('Print Receipt') }}
        </a>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row align-items-center mb-4">
    <div class="col-6">
        <h2 class="fw-bold d-flex align-items-center">
            <i class="fas fa-columns me-2 text-dark bg-light p-2 rounded shadow-sm"></i>
            {{ __('app.queue_title') }}
        </h2>
    </div>
    <div class="col-6 text-end">
        <a href="{{ route('patients.create') }}" class="btn btn-primary px-4 fw-bold">
            <i class="fas fa-user-plus me-1"></i> {{ __('app.new_patient') }}
        </a>
    </div>
</div>

<div class="row g-3 queue-container" style="overflow-x: auto; flex-wrap: nowrap; padding-bottom: 20px;">
    <!-- Column: Waiting -->
    <div class="col-md-2" style="min-width: 260px;">
        <div class="queue-column">
            <div class="column-header bg-secondary text-white d-flex justify-content-between align-items-center p-3 rounded-top">
                <h6 class="mb-0 fw-bold">{{ __('app.waiting') }}</h6>
                <span class="badge bg-white text-dark rounded-pill">{{ count($waiting) }}</span>
            </div>
            <div class="column-body p-2 bg-light border border-top-0 rounded-bottom" style="min-height: 70vh;">
                @foreach($waiting as $patient)
                    @include('queue.partials.card', ['patient' => $patient, 'status' => 'waiting'])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: Sample Collected -->
    <div class="col-md-2" style="min-width: 260px;">
        <div class="queue-column">
            <div class="column-header bg-info text-white d-flex justify-content-between align-items-center p-3 rounded-top">
                <h6 class="mb-0 fw-bold">{{ __('app.sample_collected') }}</h6>
                <span class="badge bg-white text-dark rounded-pill">{{ count($sampleCollected) }}</span>
            </div>
            <div class="column-body p-2 bg-light border border-top-0 rounded-bottom" style="min-height: 70vh;">
                @foreach($sampleCollected as $patient)
                    @include('queue.partials.card', ['patient' => $patient, 'status' => 'sample_collected'])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: In Progress -->
    <div class="col-md-2" style="min-width: 260px;">
        <div class="queue-column">
            <div class="column-header bg-warning text-dark d-flex justify-content-between align-items-center p-3 rounded-top">
                <h6 class="mb-0 fw-bold">{{ __('app.in_progress') }}</h6>
                <span class="badge bg-dark text-white rounded-pill">{{ count($inProgress) }}</span>
            </div>
            <div class="column-body p-2 bg-light border border-top-0 rounded-bottom" style="min-height: 70vh;">
                @foreach($inProgress as $patient)
                    @include('queue.partials.card', ['patient' => $patient, 'status' => 'in_progress'])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: Ready -->
    <div class="col-md-2" style="min-width: 260px;">
        <div class="queue-column">
            <div class="column-header bg-success text-white d-flex justify-content-between align-items-center p-3 rounded-top">
                <h6 class="mb-0 fw-bold">{{ __('app.ready') }}</h6>
                <span class="badge bg-white text-dark rounded-pill">{{ count($ready) }}</span>
            </div>
            <div class="column-body p-2 bg-light border border-top-0 rounded-bottom" style="min-height: 70vh;">
                @foreach($ready as $patient)
                    @include('queue.partials.card', ['patient' => $patient, 'status' => 'ready'])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: Printed/Delivered -->
    <div class="col-md-2" style="min-width: 260px;">
        <div class="queue-column">
            <div class="column-header bg-dark text-white d-flex justify-content-between align-items-center p-3 rounded-top">
                <h6 class="mb-0 fw-bold">{{ __('app.printed_delivered') }}</h6>
                <span class="badge bg-white text-dark rounded-pill">{{ count($printedDelivered) }}</span>
            </div>
            <div class="column-body p-2 bg-light border border-top-0 rounded-bottom" style="min-height: 70vh;">
                @foreach($printedDelivered as $patient)
                    @include('queue.partials.card', ['patient' => $patient, 'status' => 'printed_delivered'])
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Sample Collection Modal -->
<div class="modal fade" id="queueCollectSampleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('samples.store') }}" method="POST">
                @csrf
                <input type="hidden" name="request_id" id="queue_request_id" value="">
                <div class="modal-header py-3">
                    <h5 class="modal-title fw-bold">{{ __('app.collect_sample') ?? 'Collect Sample' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('app.sample_type') ?? 'Sample Type' }}</label>
                        <select name="sample_type" id="queue_sample_type" class="form-select form-select-sm" required>
                            <option value="">{{ __('app.select_sample_type') ?? 'Select sample type...' }}</option>
                            @foreach(\App\Models\SpecimenType::all() as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 text-muted small">
                        {{ __('app.sample_will_be_recorded_with_current_time_and_technician') ?? 'The sample will be recorded with current time and technician.' }}
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('app.record_sample') ?? 'Record Sample' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#queueCollectSampleModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const requestId = button.data('request-id');
            const patientName = button.data('patient-name');
            $('#queue_request_id').val(requestId);
            $('#queueCollectSampleModal .modal-title').text('{{ __('app.collect_sample') ?? 'Collect Sample' }} - ' + patientName);
        });
    });
</script>

<style>
    .queue-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f8fafc;
    }
    .queue-container::-webkit-scrollbar {
        height: 8px;
    }
    .queue-container::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .queue-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e0;
        border-radius: 10px;
    }
    .queue-card {
        transition: transform 0.2s;
        cursor: grab;
    }
    .queue-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
