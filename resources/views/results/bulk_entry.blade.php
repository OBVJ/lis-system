@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-flask me-2"></i>{{ __('app.bulk_results_entry') ?? 'Bulk Results Entry' }}</h4>
                            <p class="mb-0 opacity-75">{{ __('app.enter_all_results_at_once') ?? 'Enter all test results for this request at once' }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-primary fs-6 px-3 py-2">{{ $labRequest->items->count() }} {{ __('app.tests') ?? 'Tests' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Patient Info -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2"><i class="fas fa-user me-2"></i>{{ __('app.patient_info') ?? 'Patient Information' }}</h6>
                            <p class="mb-1"><strong>{{ __('app.name') ?? 'Name' }}:</strong> {{ $labRequest->patient->name }}</p>
                            <p class="mb-1"><strong>{{ __('app.patient_code') ?? 'Code' }}:</strong> {{ $labRequest->patient->patient_code }}</p>
                            <p class="mb-0"><strong>{{ __('app.age') ?? 'Age' }}:</strong> {{ $labRequest->patient->age }} {{ __('app.years') ?? 'years' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2"><i class="fas fa-calendar me-2"></i>{{ __('app.request_info') ?? 'Request Information' }}</h6>
                            <p class="mb-1"><strong>{{ __('app.request_id') ?? 'Request ID' }}:</strong> #{{ $labRequest->id }}</p>
                            <p class="mb-1"><strong>{{ __('app.priority') ?? 'Priority' }}:</strong>
                                <span class="badge bg-{{ $labRequest->priority === 'urgent' ? 'danger' : 'secondary' }}">
                                    {{ ucfirst($labRequest->priority) }}
                                </span>
                            </p>
                            <p class="mb-0"><strong>{{ __('app.created') ?? 'Created' }}:</strong> {{ $labRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Form -->
            <form action="{{ route('results.bulk.store', $labRequest) }}" method="POST" id="bulkResultsForm">
                @csrf

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>{{ __('app.enter_results') ?? 'Enter Results' }}</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.test_name') ?? 'Test Name' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.category') ?? 'Category' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.reference_range') ?? 'Reference Range' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.result_value') ?? 'Result Value' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.unit') ?? 'Unit' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.flag') ?? 'Flag' }}</th>
                                        <th class="border-0 fw-bold text-primary">{{ __('app.notes') ?? 'Notes' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($labRequest->items as $item)
                                    <tr>
                                        <td class="fw-bold">
                                            {{ $item->test->name }}
                                            @if($item->result)
                                                <span class="badge bg-success ms-2">{{ __('app.completed') ?? 'Completed' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->test->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($item->test->normal_min && $item->test->normal_max)
                                                {{ $item->test->normal_min }} - {{ $item->test->normal_max }}
                                            @else
                                                {{ __('app.not_specified') ?? 'Not specified' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->result)
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                       value="{{ $item->result->result_value }}" readonly>
                                            @else
                                                <input type="number" step="0.01" name="results[{{ $item->id }}][result_value]"
                                                       class="form-control form-control-sm result-input"
                                                       placeholder="{{ __('app.enter_value') ?? 'Enter value' }}" required>
                                            @endif
                                        </td>
                                        <td>{{ $item->test->unit }}</td>
                                        <td>
                                            @if($item->result)
                                                <span class="badge bg-{{ $item->result->flag === 'high' ? 'danger' : ($item->result->flag === 'low' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($item->result->flag) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary flag-display" data-item-id="{{ $item->id }}">
                                                    {{ __('app.pending') ?? 'Pending' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->result)
                                                <textarea class="form-control form-control-sm" rows="1" readonly>{{ $item->result->notes }}</textarea>
                                            @else
                                                <textarea name="results[{{ $item->id }}][notes]" class="form-control form-control-sm"
                                                          rows="1" placeholder="{{ __('app.optional_notes') ?? 'Optional notes' }}"></textarea>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">{{ __('app.completed_tests') ?? 'Completed' }}: <span id="completedCount">0</span> / {{ $labRequest->items->count() }}</span>
                            </div>
                            <div>
                                <a href="{{ route('queue') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i>{{ __('app.back_to_queue') ?? 'Back to Queue' }}
                                </a>
                                <button type="submit" class="btn btn-success btn-lg px-4" id="saveBtn">
                                    <i class="fas fa-save me-2"></i>{{ __('app.save_all_results') ?? 'Save All Results' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-flag results based on reference ranges
    $('.result-input').on('input', function() {
        const $row = $(this).closest('tr');
        const itemId = $row.find('.flag-display').data('item-id');
        const value = parseFloat($(this).val());

        if (!isNaN(value)) {
            // Get reference ranges from the table (this is a simplified version)
            const rangeText = $row.find('td:nth-child(3)').text().trim();
            let flag = 'normal';
            let flagClass = 'success';

            if (rangeText && rangeText !== '{{ __("app.not_specified") ?? "Not specified" }}') {
                const ranges = rangeText.split(' - ');
                if (ranges.length === 2) {
                    const min = parseFloat(ranges[0]);
                    const max = parseFloat(ranges[1]);

                    if (!isNaN(min) && !isNaN(max)) {
                        if (value > max) {
                            flag = 'high';
                            flagClass = 'danger';
                        } else if (value < min) {
                            flag = 'low';
                            flagClass = 'warning';
                        }
                    }
                }
            }

            $row.find('.flag-display')
                .removeClass('bg-secondary bg-success bg-danger bg-warning')
                .addClass(`bg-${flagClass}`)
                .text(flag.charAt(0).toUpperCase() + flag.slice(1));
        }
    });

    // Update completed count
    function updateCompletedCount() {
        const totalTests = {{ $labRequest->items->count() }};
        const completedTests = {{ $labRequest->items->whereNotNull('result')->count() }};
        $('#completedCount').text(completedTests);
    }

    updateCompletedCount();

    // Form validation
    $('#bulkResultsForm').on('submit', function(e) {
        const $emptyInputs = $('.result-input:required').filter(function() {
            return !$(this).val().trim();
        });

        if ($emptyInputs.length > 0) {
            e.preventDefault();
            alert('{{ __("app.please_fill_all_required_results") ?? "Please fill all required result values" }}');
            $emptyInputs.first().focus();
            return false;
        }

        // Show loading state
        $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("app.saving") ?? "Saving" }}...');
    });
});
</script>
@endpush

<style>
.table th {
    border-top: none;
    font-size: 0.9rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.result-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.flag-display {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endsection