@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.lab_workbench') ?? 'Lab Workbench' }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.doctor_dashboard') ?? 'Doctor Dashboard' }}</h2>
    </div>
</div>

<div class="row">
    <!-- Sample Collection Section -->
    <div class="col-md-6 mb-4">
        <div class="card card-lis shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-warning">
                    <i class="fas fa-vial me-2"></i>{{ __('app.pending_sample_collection') ?? 'Pending Sample Collection' }}
                </h6>
                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                    {{ $requests->where('status', 'pending')->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-2">{{ __('app.request_id') }}</th>
                                <th class="py-2">{{ __('app.patient_name') }}</th>
                                <th class="py-2">{{ __('app.tests') }}</th>
                                <th class="text-end pe-4 py-2">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests->where('status', 'pending')->take(5) as $request)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-primary fw-bold">#REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $request->patient->name }}</div>
                                    <small class="text-muted">{{ $request->patient->patient_code }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->items->count() }} {{ __('app.tests') }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('samples.show', $request->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-vial me-1"></i> {{ __('app.collect_sample') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    <small class="text-muted">{{ __('app.no_pending_samples') ?? 'No pending samples' }}</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Entry Section -->
    <div class="col-md-6 mb-4">
        <div class="card card-lis shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-info">
                    <i class="fas fa-microscope me-2"></i>{{ __('app.pending_results') ?? 'Pending Results Entry' }}
                </h6>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                    {{ $requests->whereIn('status', ['collected', 'sample_collected', 'in_progress'])->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-2">{{ __('app.request_id') }}</th>
                                <th class="py-2">{{ __('app.patient_name') }}</th>
                                <th class="py-2">{{ __('app.tests') }}</th>
                                <th class="text-end pe-4 py-2">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests->whereIn('status', ['collected', 'sample_collected', 'in_progress'])->take(5) as $request)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-primary fw-bold">#REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $request->patient->name }}</div>
                                    <small class="text-muted">{{ $request->patient->patient_code }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->items->where('result', '!=', null)->count() }}/{{ $request->items->count() }} {{ __('app.completed') }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('results.edit', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit me-1"></i> {{ __('app.enter_results') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    <small class="text-muted">{{ __('app.no_pending_results') ?? 'No pending results' }}</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Completed Results Section -->
<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-success">
            <i class="fas fa-check-circle me-2"></i>{{ __('app.completed_results') ?? 'Completed Results - Ready for Printing' }}
        </h6>
        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
            {{ $requests->where('status', 'completed')->count() }}
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">{{ __('app.request_id') }}</th>
                        <th class="py-3">{{ __('app.patient_name') }}</th>
                        <th class="py-3">{{ __('app.tests_requested') }}</th>
                        <th class="py-3">{{ __('app.completed_at') }}</th>
                        <th class="text-end pe-4 py-3">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests->where('status', 'completed') as $request)
                    <tr>
                        <td class="ps-4">
                            <span class="text-primary fw-bold">#REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $request->patient->name }}</div>
                            <small class="text-muted">{{ $request->patient->patient_code }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($request->items->take(3) as $item)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                                        {{ $item->test->name }}
                                    </span>
                                @endforeach
                                @if($request->items->count() > 3)
                                    <span class="small text-muted">+{{ $request->items->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ $request->updated_at ? \Carbon\Carbon::parse($request->updated_at)->format('M d, H:i') : __('app.not_available') }}
                            </small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('requests.show', $request->id) }}" class="btn btn-success btn-sm" target="_blank">
                                    <i class="fas fa-print me-1"></i> {{ __('app.print_report') ?? 'Print Report' }}
                                </a>
                                <a href="{{ route('requests.show', $request->id) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-eye me-1"></i> {{ __('app.view') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-check-circle fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('app.no_completed_results') ?? 'No completed results yet' }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
