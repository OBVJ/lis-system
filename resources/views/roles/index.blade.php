@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="mb-0 text-primary fw-bold"><i class="fas fa-user-shield me-2"></i> {{ __('app.roles_title') }}</h2>
        <p class="text-muted mb-0">{{ __('app.roles_title') }}</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> {{ __('app.add_role') }}
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">{{ __('app.role_name') }}</th>
                        <th class="px-4 py-3">{{ __('app.permissions') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ ucfirst($role->name) }}</td>
                        <td class="px-4 py-3 text-wrap overflow-hidden" style="max-width: 400px;">
                            @if($role->name === 'admin')
                                <span class="badge bg-success rounded-pill px-3 py-2">{{ __('app.all') }} {{ __('app.permissions') }}</span>
                            @else
                                @foreach($role->permissions as $permission)
                                    <span class="badge bg-soft-primary text-primary mb-1 me-1 border border-primary-subtle">{{ str_replace('_', ' ', $permission->name) }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            @if($role->name !== 'admin')
                            <div class="btn-group">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('app.are_you_sure') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('app.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">{{ __('app.no_roles_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
