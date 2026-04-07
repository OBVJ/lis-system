@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="form-card">
            <h5 class="fw-bold mb-4">{{ __('app.edit_patient') }}</h5>
            <form action="{{ route('patients.update', $patient) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">{{ __('app.full_name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $patient->name) }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('app.age') }}</label>
                        <input type="number" name="age" class="form-control" value="{{ old('age', $patient->age) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('app.gender') }}</label>
                        <select name="gender" class="form-select" required>
                            <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>{{ __('app.male') }}</option>
                            <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>{{ __('app.female') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('app.patient_type') }}</label>
                        <select name="patient_type" class="form-select">
                            <option value="">{{ __('app.select') }}</option>
                            <option value="in_patient" {{ $patient->patient_type == 'in_patient' ? 'selected' : '' }}>{{ __('app.in_patient') }}</option>
                            <option value="out_patient" {{ $patient->patient_type == 'out_patient' ? 'selected' : '' }}>{{ __('app.out_patient') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('app.treating_doctor') }}</label>
                        <input type="text" name="treating_doctor" class="form-control" value="{{ old('treating_doctor', $patient->treating_doctor) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('app.referring_doctor') }}</label>
                    <input type="text" name="referring_doctor" class="form-control" value="{{ old('referring_doctor', $patient->referring_doctor) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('app.phone') }}</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
                </div>
                <div class="mb-4">
                    <label class="form-label">{{ __('app.address') }}</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $patient->address) }}</textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-1"></i> {{ __('app.back') }}</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-check me-1"></i> {{ __('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
