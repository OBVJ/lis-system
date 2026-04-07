@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-md-10">
        <div class="card card-lis shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-file-medical-alt me-2"></i> {{ __('app.create_lab_request') }}</h5>
                <span class="text-muted small">{{ __('app.created_at') }}: {{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('requests.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold mb-3 text-uppercase small text-muted">1. {{ __('app.select_patient') }}</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">{{ __('app.search') }} {{ __('app.patients') }}</label>
                                <select name="patient_id" id="patient_select" class="form-select select2" required>
                                    <option value="" selected disabled>{{ __('app.select') }}...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->name }} ({{ $patient->patient_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="p-3 bg-light rounded small" id="patient_info" style="display:none;">
                                <div class="row">
                                    <div class="col-6 mb-2"><strong>{{ __('app.age') }}:</strong> <span id="p_age">--</span></div>
                                    <div class="col-6 mb-2"><strong>{{ __('app.gender') }}:</strong> <span id="p_gender">--</span></div>
                                    <div class="col-12"><strong>{{ __('app.phone_number') }}:</strong> <span id="p_phone">--</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-uppercase small text-muted">2. {{ __('app.select_tests') }}</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">{{ __('app.test_catalog') }}</label>
                                <select name="test_ids[]" id="test_select" class="form-select select2" multiple required>
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}" data-price="{{ $test->price }}">{{ $test->name }} ({{ $test->price }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-4 p-3 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ __('app.total_price') }}:</span>
                                    <span class="fw-bold" id="subtotal_price">0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">{{ __('app.total_price') }}:</h5>
                                    <h5 class="fw-bold mb-0 text-primary" id="total_price">0.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="{{ route('requests.index') }}" class="btn btn-light px-4 border">
                            <i class="fas fa-times me-1"></i> {{ __('app.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-lis-success px-5 shadow-sm fw-bold">
                            <i class="fas fa-check-circle me-1"></i> {{ __('app.submit_request') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        $('#test_select').on('change', function() {
            let total = 0;
            $('#test_select option:selected').each(function() {
                total += parseFloat($(this).data('price'));
            });
            $('#subtotal_price, #total_price').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        });
    });
</script>
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6;
        min-height: 38px;
    }
</style>
@endpush
@endsection
