@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">Laboratory Workbench</h3>
        <h2 class="fw-bold mb-0">Result Entry (Bulk)</h2>
    </div>
</div>

<div class="card card-lis shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-3">
                <label class="text-uppercase small fw-bold text-muted d-block">Patient Name</label>
                <h5 class="fw-bold text-primary">{{ $request->patient->name }}</h5>
            </div>
            <div class="col-md-2">
                <label class="text-uppercase small fw-bold text-muted d-block">Patient ID</label>
                <h5 class="fw-bold text-dark">{{ $request->patient->patient_code }}</h5>
            </div>
            <div class="col-md-2">
                <label class="text-uppercase small fw-bold text-muted d-block">Request #</label>
                <h5 class="fw-bold text-dark">REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</h5>
            </div>
            <div class="col-md-3">
                <label class="text-uppercase small fw-bold text-muted d-block">Tests in Order</label>
                <h5 class="fw-bold text-dark">{{ $request->items->count() }} Test(s)</h5>
            </div>
        </div>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <form action="{{ route('results.bulk.store', $request->id) }}" method="POST">
        @csrf
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="result-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Test Name</th>
                            <th class="py-3">Normal Range</th>
                            <th class="py-3">Unit</th>
                            <th class="py-3" style="width: 150px;">Result</th>
                            <th class="py-3">Flag</th>
                            <th class="py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($request->items as $index => $item)
                        <tr>
                            <td class="ps-4">
                                <input type="hidden" name="results[{{ $index }}][item_id]" value="{{ $item->id }}">
                                <span class="fw-bold text-dark">{{ $item->test->name }}</span>
                                <div class="small text-muted">{{ $item->test->category->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1" 
                                      data-min="{{ $item->test->normal_min }}" 
                                      data-max="{{ $item->test->normal_max }}">
                                    {{ $item->test->normal_min }} - {{ $item->test->normal_max }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $item->test->unit }}</td>
                            <td>
                                <input type="number" step="0.01" 
                                       name="results[{{ $index }}][value]" 
                                       class="form-control form-control-sm result-input" 
                                       value="{{ $item->result->result_value ?? '' }}" 
                                       placeholder="Enter value" 
                                       {{ $item->status == 'completed' ? 'readonly bg-light' : '' }}>
                            </td>
                            <td>
                                <span class="flag-badge badge rounded-pill px-3 shadow-sm">
                                    {{ $item->result->flag ?? 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <input type="text" name="results[{{ $index }}][notes]" 
                                       class="form-control form-control-sm" 
                                       value="{{ $item->result->notes ?? '' }}" 
                                       placeholder="Optional notes">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0"><i class="fas fa-keyboard me-1"></i> Tip: Use [Tab] for fast navigation between input fields.</p>
            <div class="d-flex gap-2">
                <a href="{{ route('results.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-lis-primary px-5 shadow-sm fw-bold">
                    <i class="fas fa-save me-1"></i> Save All Results
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-Focus first input
        $('.result-input').first().focus();

        // Real-time Flagging
        $('.result-input').on('keyup change blur', function() {
            let value = parseFloat($(this).val());
            let row = $(this).closest('tr');
            let range = row.find('[data-min]');
            let min = parseFloat(range.data('min'));
            let max = parseFloat(range.data('max'));
            let badge = row.find('.flag-badge');

            if (isNaN(value)) {
                badge.text('Pending').removeClass('bg-success bg-danger bg-warning text-white');
                return;
            }

            badge.removeClass('bg-success bg-danger bg-warning text-white');
            if (value < min) {
                badge.text('Low').addClass('bg-warning text-dark');
            } else if (value > max) {
                badge.text('High').addClass('bg-danger text-white');
            } else {
                badge.text('Normal').addClass('bg-success text-white');
            }
        });

        // Trigger on load for existing results
        $('.result-input').trigger('change');
    });
</script>
@endpush
@endsection
