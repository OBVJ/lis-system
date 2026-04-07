@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.patients_database') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.record_management') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('patients.create') }}" class="btn btn-lis-primary">
            <i class="fas fa-plus me-1"></i> {{ __('app.new_patient') }}
        </a>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <form action="{{ route('patients.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('app.search_placeholder_patient') }}" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col text-end">
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-2"><i class="fas fa-filter me-1"></i> {{ __('app.filter') }}</button>
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-file-export me-1"></i> {{ __('app.export') }}</button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">{{ __('app.patient_code') }}</th>
                        <th class="py-3">{{ __('app.full_name') }}</th>
                        <th class="py-3">{{ __('app.age') }}</th>
                        <th class="py-3">{{ __('app.gender') }}</th>
                        <th class="py-3">{{ __('app.phone_number') }}</th>
                        <th class="py-3 text-center">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td class="ps-4"><span class="badge bg-light text-dark border">{{ $patient->patient_code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $patient->name }}</div>
                            <small class="text-muted">{{ $patient->email ?? __('app.no_email') }}</small>
                        </td>
                        <td>{{ $patient->age }} {{ __('app.years') }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $patient->gender == 'male' ? 'bg-primary' : 'bg-danger' }} bg-opacity-10 {{ $patient->gender == 'male' ? 'text-primary' : 'text-danger' }} px-3">
                                {{ $patient->gender == 'male' ? __('app.male') : __('app.female') }}
                            </span>
                        </td>
                        <td>{{ $patient->phone }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('app.view_history') }}">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.edit_profile') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('app.delete') }}" onclick="if(confirm('{{ __('app.are_you_sure') }}')) document.getElementById('delete-form-{{ $patient->id }}').submit();">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $patient->id }}" action="{{ route('patients.destroy', $patient) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-folder-open fs-1 d-block mb-3"></i>
                                {{ __('app.no_patients_found') }}
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $patients->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
