@extends('layouts.app')

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --lis-blue: #0d6efd;
        --lis-blue-light: #e7f1ff;
        --lis-green: #198754;
        --lis-red: #dc3545;
        --lis-border: #ced4da;
    }
    .form-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .form-header {
        background: #f8f9fa;
        padding: 15px 25px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-body {
        padding: 25px;
    }
    .input-icon-group {
        position: relative;
    }
    .input-icon-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    html[dir="rtl"] .input-icon-group i {
        left: auto;
        right: 15px;
    }
    .custom-input {
        padding: 12px 15px 12px 45px;
        border-radius: 8px;
        border: 1px solid var(--lis-border);
        transition: all 0.3s;
        height: 50px;
    }
    html[dir="rtl"] .custom-input {
        padding: 12px 45px 12px 15px;
    }
    .custom-input:focus {
        border-color: var(--lis-blue);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .custom-select {
        height: 50px;
        padding-left: 15px;
        border-radius: 8px;
    }
    
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 50px;
        border-radius: 8px;
        border: 1px solid var(--lis-border);
        padding-top: 5px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--lis-blue);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .btn-action {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .loading-spinner {
        display: none;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11">
        <div class="text-center mb-4">
            <h1 class="display-6 fw-bold text-primary mb-2">
                <i class="fas fa-user-plus me-3"></i>{{ __('app.patient_registration_billing') ?? 'Patient Registration & Billing' }}
            </h1>
            <p class="text-muted">{{ __('app.register_patient_select_tests_process_payment') ?? 'Register patient, select tests, and process payment all in one place' }}</p>
        </div>
        <form action="{{ route('patients.store') }}" method="POST" id="patientForm" novalidate>
            @csrf
            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
            <div class="row g-4">
                <div class="col-xl-5">
                    <!-- Patient Search Section -->
                    <div class="form-section">
                        <div class="form-header" style="background: #fff3cd; border-bottom-color: rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-search text-warning"></i>
                            {{ __('app.search_existing_patient') ?? 'Search Existing Patient' }}
                        </div>
                        <div class="form-body">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">{{ __('app.search_patient') ?? 'Search Patient' }}</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="patient_search" class="form-control custom-input" placeholder="{{ __('app.search_by_name_code_phone') ?? 'Search by name, patient code, or phone...' }}" autocomplete="off">
                                    </div>
                                    <div class="form-text">{{ __('app.search_existing_patient_help') ?? 'Search for existing patients to auto-fill information' }}</div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" id="new_patient_button" class="btn btn-outline-primary btn-action w-100" onclick="clearPatientForm()">
                                        <i class="fas fa-plus me-2"></i>{{ __('app.new_patient') ?? 'New Patient' }}
                                    </button>
                                </div>
                            </div>
                            <div id="search_results" class="mt-3" style="display: none;">
                                <div class="list-group" id="patient_list"></div>
                            </div>
                            <div id="selected_patient_alert" class="alert alert-info mt-3 d-none">
                                <strong>{{ __('app.selected_patient') ?? 'Selected patient:' }}</strong>
                                <span id="selected_patient_name"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Info Section -->
                    <div class="form-section">
                        <div class="form-header">
                            <i class="fas fa-user-injured text-primary"></i>
                            {{ __('app.patient_info') ?? 'Patient Information' }}
                        </div>
                        <div class="form-body">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('app.full_name') }} <span class="text-danger">*</span></label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="name" class="form-control custom-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('app.full_name') }}" required>
                                    </div>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.age') }} <span class="text-danger">*</span></label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="number" name="age" class="form-control custom-input @error('age') is-invalid @enderror" value="{{ old('age') }}" placeholder="e.g. 35" required min="0">
                                    </div>
                                    @error('age') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.gender') }} <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select custom-select @error('gender') is-invalid @enderror" required>
                                        <option value="" selected disabled>{{ __('app.select') }}...</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('app.male') }}</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('app.female') }}</option>
                                    </select>
                                    @error('gender') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.phone_number') }}</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-phone"></i>
                                        <input type="text" name="phone" class="form-control custom-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+123456789">
                                    </div>
                                    @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.patient_type') }}</label>
                                    <select name="patient_type" class="form-select custom-select @error('patient_type') is-invalid @enderror">
                                        <option value="" selected disabled>{{ __('app.select') }}...</option>
                                        <option value="in_patient" {{ old('patient_type') == 'in_patient' ? 'selected' : '' }}>{{ __('app.in_patient') }}</option>
                                        <option value="out_patient" {{ old('patient_type') == 'out_patient' ? 'selected' : '' }}>{{ __('app.out_patient') }}</option>
                                    </select>
                                    @error('patient_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('app.address') }}</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" style="border-radius: 8px; padding: 12px; border: 1px solid var(--lis-border);">{{ old('address') }}</textarea>
                                    @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.treating_doctor') }}</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-user-md"></i>
                                        <input type="text" name="treating_doctor" class="form-control custom-input @error('treating_doctor') is-invalid @enderror" value="{{ old('treating_doctor') }}" placeholder="Dr. John Doe">
                                    </div>
                                    @error('treating_doctor') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('app.referring_doctor') }}</label>
                                    <div class="input-icon-group">
                                        <i class="fas fa-stethoscope"></i>
                                        <input type="text" name="referring_doctor" class="form-control custom-input @error('referring_doctor') is-invalid @enderror" value="{{ old('referring_doctor') }}" placeholder="Clinic name or doctor">
                                    </div>
                                    @error('referring_doctor') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <!-- Test Selection Section -->
            <div class="form-section">
                <div class="form-header" style="background: var(--lis-blue-light); border-bottom-color: rgba(13, 110, 253, 0.1);">
                    <i class="fas fa-flask text-primary"></i>
                    {{ __('app.select_tests') ?? 'Select Tests' }}
                </div>
                <div class="form-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('app.requested_tests') }} <span class="text-danger">*</span></label>
                            <select name="test_ids[]" id="test_select" class="form-select select2" multiple required>
                                @if(old('test_ids'))
                                    @foreach(old('test_ids') as $testId)
                                        @php $test = \App\Models\LabTest::find($testId); @endphp
                                        @if($test)
                                            <option value="{{ $test->id }}" selected>{{ $test->name }} ({{ $test->category->name ?? '' }}) - {{ \App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($test->price, 2) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('test_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div class="form-text">{{ __('app.select_multiple_tests') ?? 'You can select multiple tests. Start typing to search.' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.priority') ?? 'Priority' }}</label>
                            <select id="priority" name="priority" class="form-select custom-select">
                                <option value="normal" selected>{{ __('app.normal') ?? 'Normal' }}</option>
                                <option value="urgent">{{ __('app.urgent') ?? 'Urgent' }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.notes') ?? 'Notes' }}</label>
                            <textarea id="notes" name="notes" class="form-control" rows="2" style="border-radius: 8px; padding: 12px; border: 1px solid var(--lis-border);" placeholder="{{ __('app.additional_notes') ?? 'Additional notes...' }}">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="form-section">
                <div class="form-header" style="background: var(--lis-green); border-bottom-color: rgba(25, 135, 84, 0.1);">
                    <i class="fas fa-money-bill-wave text-success"></i>
                    {{ __('app.payment_billing') ?? 'Payment & Billing' }}
                </div>
                <div class="form-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.discount_type') ?? 'Discount Type' }}</label>
                            <select name="discount_type" class="form-select custom-select" id="discount_type">
                                <option value="">{{ __('app.no_discount') ?? 'No Discount' }}</option>
                                <option value="fixed">{{ __('app.fixed_amount') ?? 'Fixed Amount' }}</option>
                                <option value="percentage">{{ __('app.percentage') ?? 'Percentage' }}</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="discount_value_container" style="display: none;">
                            <label class="form-label fw-bold">{{ __('app.discount_value') ?? 'Discount Value' }}</label>
                            <div class="input-icon-group">
                                <i class="fas fa-percent" id="discount_icon"></i>
                                <input type="number" name="discount_value" class="form-control custom-input" value="{{ old('discount_value') }}" placeholder="0" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">{{ __('app.subtotal') ?? 'Subtotal' }}</label>
                                        <div class="h4 text-primary" id="subtotal">0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">{{ __('app.discount') ?? 'Discount' }}</label>
                                        <div class="h4 text-success" id="discount_amount">0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">{{ __('app.final_total') ?? 'Final Total' }}</label>
                                        <div class="h4 text-danger fw-bold" id="final_total">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.paid_amount') ?? 'Paid Amount' }}</label>
                            <div class="input-icon-group">
                                <i class="fas fa-dollar-sign"></i>
                                <input id="paid_amount" type="number" name="paid_amount" class="form-control custom-input" value="{{ old('paid_amount') }}" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('app.payment_status') ?? 'Payment Status' }}</label>
                            <div class="h5" id="payment_status">{{ __('app.unpaid') ?? 'Unpaid' }}</div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5 gap-3">
                <a href="{{ route('patients.index') }}" class="btn btn-light btn-action text-secondary border">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('app.cancel') }}
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-action" onclick="printReceipt()" id="printBtn" style="display: none;">
                        <i class="fas fa-print me-2"></i>{{ __('app.print_receipt') ?? 'Print Receipt' }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-action shadow-sm" id="submitBtn">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-save" id="submitIcon"></i>
                            <span class="loading-spinner" id="submitSpinner"></span>
                            {{ __('app.register_process_payment') ?? 'Register & Process Payment' }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Patient search functionality
        let searchTimeout;
        $('#patient_search').on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(searchTimeout);

            if (query.length < 2 && query !== '') {
                $('#search_results').hide();
                return;
            }

            searchTimeout = setTimeout(() => {
                searchPatients(query);
            }, 300);
        });

        $('#patient_search').on('focus', function() {
            const query = $(this).val().trim();
            if (query === '') {
                searchPatients('');
            }
        });

        // Test selection
        $('#test_select').select2({
            theme: 'bootstrap-5',
            placeholder: "{{ __('app.search') }}...",
            allowClear: true,
            ajax: {
                url: '{{ route('tests.ajaxSearch') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Calculate total price interactively
        $('#test_select').on('change', function() {
            calculateTotals();
        });

        // Discount type change
        $('#discount_type').on('change', function() {
            const discountType = $(this).val();
            if (discountType) {
                $('#discount_value_container').show();
                $('#discount_icon').removeClass('fa-percent fa-dollar-sign');
                if (discountType === 'percentage') {
                    $('#discount_icon').addClass('fa-percent');
                    $('#discount_value').attr('max', '100');
                } else {
                    $('#discount_icon').addClass('fa-dollar-sign');
                    $('#discount_value').removeAttr('max');
                }
            } else {
                $('#discount_value_container').hide();
                $('#discount_value').val('');
            }
            calculateTotals();
        });

        // Discount value change
        $('#discount_value').on('input', function() {
            calculateTotals();
        });

        // Paid amount change
        $('#paid_amount').on('input', function() {
            calculatePaymentStatus();
        });

        function calculateTotals() {
            let subtotal = 0;
            const selectedData = $('#test_select').select2('data');
            selectedData.forEach(function(item) {
                subtotal += parseFloat(item.price) || 0;
            });

            const discountType = $('#discount_type').val();
            const discountValue = parseFloat($('#discount_value').val()) || 0;
            let discountAmount = 0;

            if (discountType === 'percentage' && discountValue > 0) {
                discountAmount = (subtotal * discountValue) / 100;
            } else if (discountType === 'fixed' && discountValue > 0) {
                discountAmount = Math.min(discountValue, subtotal);
            }

            const finalTotal = subtotal - discountAmount;

            $('#subtotal').text(subtotal.toFixed(2));
            $('#discount_amount').text(discountAmount.toFixed(2));
            $('#final_total').text(finalTotal.toFixed(2));

            calculatePaymentStatus();
        }

        function calculatePaymentStatus() {
            const finalTotal = parseFloat($('#final_total').text()) || 0;
            const paidAmount = parseFloat($('#paid_amount').val()) || 0;

            let status = "{{ __('app.unpaid') }}";
            let statusClass = "text-danger";

            if (paidAmount >= finalTotal && finalTotal > 0) {
                status = "{{ __('app.paid') }}";
                statusClass = "text-success";
            } else if (paidAmount > 0) {
                status = "{{ __('app.partial') }}";
                statusClass = "text-warning";
            }

            $('#payment_status').text(status).removeClass('text-danger text-success text-warning').addClass(statusClass);
        }

        // Form submission loading state
        $('#patientForm').on('submit', function(e) {
            $(this).addClass('was-validated');
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            // Show loading state — let the browser submit normally
            $('#submitBtn').prop('disabled', true);
            $('#submitIcon').hide();
            $('#submitSpinner').show();
        });
    });

    function searchPatients(query) {
        $.ajax({
            url: '{{ route("patients.ajaxSearch") }}',
            method: 'GET',
            data: { q: query },
            success: function(data) {
                displaySearchResults(data);
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                $('#search_results').hide();
            }
        });
    }

    function displaySearchResults(patients) {
        const $results = $('#patient_list');
        $results.empty();
        
        if (patients.length === 0) {
            $results.append('<div class="list-group-item text-muted">{{ __("app.no_patients_found") ?? "No patients found" }}</div>');
        } else {
            patients.forEach(function(patient) {
                const patientItem = `
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectPatient(${JSON.stringify(patient).replace(/"/g, '&quot;')})">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${patient.name}</h6>
                            <small class="text-muted">${patient.patient_code}</small>
                        </div>
                        <p class="mb-1">${patient.age} years old • ${patient.gender} • ${patient.phone || '{{ __("app.no_phone") ?? "No phone" }}'}</p>
                        <small class="text-muted">${patient.patient_type ? patient.patient_type.replace('_', ' ') : ''}</small>
                    </button>
                `;
                $results.append(patientItem);
            });
        }
        
        $('#search_results').show();
    }

    function selectPatient(patient) {
        // Auto-fill patient information directly from search results
        $('#patient_id').val(patient.id);
        $('input[name="name"]').val(patient.name);
        $('input[name="age"]').val(patient.age);
        $('select[name="gender"]').val(patient.gender);
        $('input[name="phone"]').val(patient.phone || '');
        $('select[name="patient_type"]').val(patient.patient_type || '');
        $('textarea[name="address"]').val(patient.address || '');
        
        // Show selected patient notice
        $('#selected_patient_name').text(patient.patient_code ? `${patient.name} (${patient.patient_code})` : patient.name);
        $('#selected_patient_alert').removeClass('d-none');

        // Hide search results
        $('#search_results').hide();
        $('#patient_search').val('');
        
        showToast('{{ __("app.patient_loaded") ?? "Patient information loaded successfully" }}', 'success');
    }

    function clearPatientForm() {
        // Clear the selected patient marker
        $('#patient_id').val('');
        $('#selected_patient_name').text('');
        $('#selected_patient_alert').addClass('d-none');

        // Clear all patient fields
        $('input[name="name"]').val('');
        $('input[name="age"]').val('');
        $('select[name="gender"]').val('');
        $('input[name="phone"]').val('');
        $('select[name="patient_type"]').val('');
        $('textarea[name="address"]').val('');
        
        // Clear search
        $('#patient_search').val('');
        $('#search_results').hide();
        
        // Clear test selection
        $('#test_select').val(null).trigger('change');
        $('#priority').val('normal');
        $('#notes').val('');
        
        // Reset payment
        $('#discount_type').val('').trigger('change');
        $('#discount_value').val('');
        $('#paid_amount').val('');
        calculateTotals();
        
        showToast('{{ __("app.form_cleared") ?? "Form cleared for new patient" }}', 'info');
    }

    function showToast(message, type) {
        // Simple toast notification - you can replace with a proper toast library
        const toastClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const toast = $(`<div class="alert ${toastClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        
        $('body').append(toast);
        setTimeout(() => toast.alert('close'), 3000);
    }
</script>
@endpush
@endsection
