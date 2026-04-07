@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="mb-0 text-primary fw-bold"><i class="fas fa-edit me-2"></i> Edit Role: {{ ucfirst($role->name) }}</h2>
        <p class="text-muted mb-0">Update role details and permissions</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('roles.index') }}" class="btn btn-light shadow-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Roles
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label fw-bold">Role Name</label>
                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <h5 class="fw-bold mb-3 border-bottom pb-2">Assign Access Permissions</h5>
            
            <div class="row g-3">
                @foreach($permissions as $permission)
                <div class="col-md-4">
                    <div class="form-check form-switch custom-switch">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="perm_{{ $permission->id }}">
                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 text-end">
                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                    <i class="fas fa-save me-2"></i> Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
