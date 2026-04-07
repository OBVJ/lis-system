@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 px-4 py-5 shadow-lg border-0 border-top border-primary border-5 rounded-10 scale-up">
        <h3 class="mb-4 fw-bold px-2"><i class="fas fa-plus-circle me-2 text-primary"></i>Enter Test Result</h3>
        <hr class="my-4 opacity-50">
        
        <div class="p-3 bg-light rounded-5 mb-4 shadow-sm">
            <h5 class="fw-bold mb-2">{{ $item->test->name }}</h5>
            <p class="text-muted fw-bold small mb-2"><i class="fas fa-user-circle me-2"></i>Patient: {{ $item->request->patient->name }}</p>
            <p class="mb-0 text-primary fw-bold small"><i class="fas fa-info-circle me-2"></i>Reference: {{ $item->test->normal_min }} - {{ $item->test->normal_max }} {{ $item->test->unit }}</p>
        </div>

        <form action="{{ route('results.store') }}" method="POST">
            @csrf
            <input type="hidden" name="request_item_id" value="{{ $item->id }}">
            
            <div class="mb-4 px-2">
                <label class="form-label fw-bold text-muted">Result Value ({{ $item->test->unit }})</label>
                <input type="number" step="0.01" name="result_value" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" placeholder="Ex: 12.5" required>
            </div>

            <div class="mb-5 px-2">
                <label class="form-label fw-bold text-muted">Notes / Clinical Observations</label>
                <textarea name="notes" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" placeholder="Enter findings..."></textarea>
            </div>

            <div class="d-flex justify-content-between px-2 mt-4 gap-2">
                <a href="{{ route('requests.show', $item->request_id) }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 flex-grow-1">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow flex-grow-1 animate-pop"><i class="fas fa-check-circle me-1"></i>Save Result</button>
            </div>
        </form>
    </div>
</div>

<style>
    .rounded-10 { border-radius: 20px; }
    .scale-up { transition: all 0.3s ease; }
    .scale-up:hover { transform: scale(1.02); }
    .animate-pop:active { transform: scale(0.95); }
</style>
@endsection
