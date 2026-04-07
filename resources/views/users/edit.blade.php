@extends('layouts.app')

@section('content')
<div class="mb-4 mt-2">
    <a href="{{ route('users.index') }}" class="btn btn-sm fw-bold mb-3" style="background:#f1f1f4;color:#7e8299;border-radius:6px;">
        <i class="fas fa-arrow-left me-1"></i> Back to Users
    </a>
    <h2 class="fw-bold mb-0">Edit User: {{ $user->name }}</h2>
    <p class="text-muted mb-0">Update account details and role assignment.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-lis shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="alert alert-danger border-0 mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li class="fw-semibold">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Repeat new password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-lg" required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Account Active</label>
                                <div class="text-muted small">Inactive users cannot log in.</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-light fw-bold px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm px-5">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
