@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user"></i> {{ $patient->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning btn-sm px-3"><i class="fas fa-edit me-1"></i>{{ __('app.edit') }}</a>
        <a href="{{ route('requests.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm px-3"><i class="fas fa-file-medical me-1"></i>{{ __('app.new_request') }}</a>
    </div>
</div>

<div class="row g-3">
    <!-- Patient Info Card -->
    <div class="col-md-4">
        <div class="stat-card text-center" style="border-top:3px solid #3498db;">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:70px;height:70px;">
                    <i class="fas fa-user text-primary fa-2x"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-1">{{ $patient->name }}</h5>
            <p class="text-muted small fw-bold">{{ $patient->patient_code }}</p>
            <hr>
            <div class="text-start px-3">
                <p class="mb-2"><i class="fas fa-birthday-cake me-2 text-primary"></i><strong>{{ __('app.age') }}:</strong> {{ $patient->age }} years</p>
                <p class="mb-2"><i class="fas fa-venus-mars me-2 text-primary"></i><strong>{{ __('app.gender') }}:</strong> {{ ucfirst($patient->gender) }}</p>
                <p class="mb-2"><i class="fas fa-phone me-2 text-primary"></i><strong>{{ __('app.phone') }}:</strong> {{ $patient->phone ?? '---' }}</p>
                <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong>{{ __('app.address') }}:</strong> {{ $patient->address ?? '---' }}</p>
            </div>
        </div>
    </div>

    <!-- Patient History -->
    <div class="col-md-8">
        <div class="stat-card" style="padding:24px;">
            <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>{{ __('app.patient_history') }}</h5>

            @forelse($patient->requests()->latest()->get() as $request)
            <div class="p-3 mb-3 rounded" style="background:#f8f9fa; border-left:3px solid {{ $request->status == 'completed' ? '#27ae60' : '#3498db' }};">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</h6>
                        <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>{{ $request->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="text-end">
                        <span class="badge-status badge-{{ $request->status }}">{{ strtoupper(str_replace('_', ' ', $request->status)) }}</span>
                        <div class="small fw-bold text-dark mt-1">${{ number_format($request->total_price, 2) }}</div>
                    </div>
                </div>
                <hr class="my-2 opacity-25">
                <div class="row">
                    @foreach($request->items as $item)
                    <div class="col-md-6 mb-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small"><i class="fas fa-flask text-muted me-1"></i>{{ $item->test->name }}</span>
                            @if($item->result)
                            <span class="badge-status badge-{{ $item->result->flag }}" style="font-size:10px;">
                                {{ $item->result->result_value }} {{ $item->test->unit }}
                            </span>
                            @else
                            <span class="text-muted small">Pending</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-2 text-end">
                    <a href="{{ route('requests.show', $request->id) }}" class="btn-action btn-view"><i class="fas fa-eye me-1"></i>{{ __('app.view_details') }}</a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                <p>{{ __('app.no_history') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
