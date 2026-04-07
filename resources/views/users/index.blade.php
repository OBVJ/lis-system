@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.admin') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.users_title') }}</h2>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary fw-bold shadow-sm px-4">
        <i class="fas fa-user-plus me-2"></i> {{ __('app.add_user') }}
    </a>
</div>

<div class="card card-lis shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">{{ __('app.username') }}</th>
                        <th>{{ __('app.email') }}</th>
                        <th>{{ __('app.role') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th>{{ __('app.created_at') }}</th>
                        <th class="text-end pe-4">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;font-size:1rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <small class="text-muted">({{ __('app.you') }})</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            @php $role = $user->getRoleNames()->first() @endphp
                            @if($role)
                                @php $colors = ['admin'=>'danger','lab_technician'=>'primary','receptionist'=>'success','doctor'=>'info'] @endphp
                                <span class="badge bg-{{ $colors[$role] ?? 'secondary' }} bg-opacity-15 text-{{ $colors[$role] ?? 'secondary' }} px-3 py-2 rounded-pill fw-bold text-capitalize">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">---</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="fas fa-circle me-1" style="font-size:.5rem"></i> {{ __('app.active') }}</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="fas fa-circle me-1" style="font-size:.5rem"></i> {{ __('app.inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light fw-bold">
                                    <i class="fas fa-edit me-1"></i> {{ __('app.edit') }}
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.toggle-active', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light fw-bold text-{{ $user->is_active ? 'warning' : 'success' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-1"></i>
                                        {{ $user->is_active ? __('app.disable') : __('app.enable') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-2x mb-3 d-block opacity-25"></i>
                            {{ __('app.no_users_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top-0 py-3 px-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
