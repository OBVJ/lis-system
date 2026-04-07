@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">System Administration</h3>
        <h2 class="fw-bold mb-0">Specimen Types</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        @include('settings._sidebar')
    </div>
    
    <div class="col-md-9">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle fs-4 me-3"></i> <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card card-lis shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0"><i class="fas fa-plus-circle me-2"></i> Add New Specimen Type</h5>
            </div>
            <div class="card-body bg-light border-top p-4">
                <form action="{{ route('settings.specimen-types.store') }}" method="POST">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-vial text-muted"></i></span>
                                <input type="text" class="form-control border-0 shadow-none fw-bold" name="name" placeholder="Type Name (e.g. Blood, Urine, Swab)" required>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm pt-2 pb-2">Save Type</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-lis shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list me-2 text-muted"></i> Available Specimen Types</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Type Name</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $type->id }}</td>
                                <td class="fw-bold fs-6">{{ $type->name }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('settings.specimen-types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger fw-bold">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-5">No specimen types found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
