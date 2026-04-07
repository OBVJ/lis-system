@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.test_orders') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.lab_request_pipeline') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('requests.create') }}" class="btn btn-lis-primary">
            <i class="fas fa-file-medical me-1"></i> {{ __('app.new_request') }}
        </a>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <form action="{{ route('requests.index') }}" method="GET" class="input-group">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="q" class="form-control" placeholder="{{ __('app.search_request') }}" value="{{ request('q') }}">
                    <button type="submit" class="input-group-text bg-primary text-white border-primary"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="col text-end">
                <div class="btn-group">
                    <a href="{{ route('requests.index', ['status' => 'all', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">{{ __('app.all') }}</a>
                    
                    <a href="{{ route('requests.index', ['status' => 'pending', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'pending' ? 'active' : '' }}">{{ __('app.pending') }}</a>
                    
                    <a href="{{ route('requests.index', ['status' => 'collected', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'collected' ? 'active' : '' }}">{{ __('app.collected') }}</a>
                    
                    <a href="{{ route('requests.index', ['status' => 'in_progress', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'in_progress' ? 'active' : '' }}">{{ __('app.in_progress') }}</a>
                    
                    <a href="{{ route('requests.index', ['status' => 'review', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'review' ? 'active' : '' }}">{{ __('app.review') }}</a>
                    
                    <a href="{{ route('requests.index', ['status' => 'completed', 'q' => request('q')]) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'completed' ? 'active' : '' }}">{{ __('app.completed') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('app.request_id') }}</th>
                        <th>{{ __('app.patient_name') }}</th>
                        <th>{{ __('app.tests_requested') }}</th>
                        <th>{{ __('app.created_at') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th class="text-end pe-4">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td class="ps-4"><span class="fw-bold text-primary">#REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $request->patient->name }}</div>
                            <small class="text-muted">{{ $request->patient->patient_code }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($request->items as $item)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                                        {{ $item->test->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge rounded-pill 
                                {{ $request->status == 'completed' ? 'bg-success' : ($request->status == 'in_progress' ? 'bg-info' : 'bg-warning') }} 
                                bg-opacity-10 
                                {{ $request->status == 'completed' ? 'text-success' : ($request->status == 'in_progress' ? 'text-info' : 'text-warning') }} 
                                px-3">
                                {{ __('app.' . $request->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i> {{ __('app.details') }}</a>
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('requests.show', $request->id) }}"><i class="fas fa-vial me-2"></i> {{ __('app.collect_sample') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('requests.show', $request->id) }}"><i class="fas fa-file-invoice me-2"></i> {{ __('app.view_invoice') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times me-2"></i> {{ __('app.cancel_request') }}</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">{{ __('app.no_requests_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0">
        {{ $requests->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
