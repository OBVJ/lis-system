@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">{{ __('app.sample_id') }}: #SMP-{{ str_pad($sample->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <p class="text-muted mb-0">{{ __('app.request_id') }}: #REQ-{{ str_pad($sample->request->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('samples.print', $sample) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fas fa-barcode me-1"></i> {{ __('app.print') }}
        </a>
        <a href="{{ route('samples.index') }}" class="btn btn-light ms-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('app.back') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-lis shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('app.patient_name') }}</h5>
                <p class="mb-1"><strong>{{ $sample->request->patient->name }}</strong></p>
                <p class="text-muted mb-1">{{ $sample->request->patient->patient_code }}</p>
                <p class="text-muted mb-1">{{ __('app.age') }}: {{ $sample->request->patient->age }} {{ __('app.years') }}</p>
                <p class="text-muted mb-0">{{ __('app.gender') }}: {{ ucfirst($sample->request->patient->gender) }}</p>
            </div>
        </div>

        <div class="card card-lis shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('app.sample_details') }}</h5>
                <p class="mb-2"><strong>{{ __('app.sample_type') }}:</strong> {{ ucfirst($sample->sample_type) }}</p>
                <p class="mb-2"><strong>{{ __('app.collected_at') }}:</strong> {{ $sample->collected_at->format('M d, Y H:i') }}</p>
                <p class="mb-2"><strong>{{ __('app.status') }}:</strong> {{ ucfirst($sample->status) }}</p>
                <p class="mb-0"><strong>{{ __('app.barcode') }}:</strong> {{ $sample->barcode }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-lis shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('app.test_results') }}</h5>
                @foreach($sample->request->items as $item)
                    <div class="mb-3 p-3 border rounded">
                        <div class="fw-bold">{{ $item->test->name }}</div>
                        <div class="small text-muted">{{ $item->test->category->name ?? '' }}</div>
                        @if($item->result)
                            <div class="mt-2">
                                <strong>{{ __('app.result_value') }}:</strong> {{ $item->result->result_value }} {{ $item->test->unit ?? '' }}
                                @if($item->result->flag)
                                    <span class="badge ms-2 
                                        @if($item->result->flag == 'High') bg-danger
                                        @elseif($item->result->flag == 'Low') bg-warning text-dark
                                        @else bg-success @endif">
                                        {{ __('app.' . strtolower($item->result->flag)) }}
                                    </span>
                                @else
                                    <span class="badge ms-2 bg-success">{{ __('app.normal') }}</span>
                                @endif
                            </div>
                            @if($item->result->notes)
                                <div class="small text-muted mt-1">{{ __('app.notes') }}: {{ $item->result->notes }}</div>
                            @endif
                        @else
                            <div class="mt-2">
                                <span class="badge bg-secondary">{{ __('app.pending') }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
