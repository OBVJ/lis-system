@extends('layouts.app')

@section('content')
<div class="mb-4 mt-2">
    <a href="{{ route('inventory.index') }}" class="btn btn-sm mb-3 fw-bold" style="background-color: #f1f4; color: #7e8299; border-radius: 6px;"><i class="fas fa-arrow-left me-1"></i> {{ __('app.back_to_inventory') }}</a>
    <h2 class="fw-bold text-dark" style="font-size: 1.5rem;">{{ __('app.stock_movement') }}: {{ $inventory->name }}</h2>
    <p class="text-muted fw-semibold">{{ __('app.current_available_stock') }}: <strong class="text-primary fs-5">{{ $inventory->current_stock }}</strong> {{ $inventory->unit }}</p>
</div>

@if(session('error'))
    <div class="alert alert-danger fw-bold p-3 border-0" style="border-radius: 8px; background-color: #fff5f8; color: #f1416c;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 800px;">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('inventory.storeTransaction', $inventory->id) }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">{{ __('app.transaction_type') }} <span class="text-danger">*</span></label>
                    <select name="type" class="form-select fw-bold" style="border-radius: 8px; padding: 12px 15px; background-color: #f9f9f9;" required id="transactionType">
                        <option value="in" style="font-weight: 700;">{{ __('app.stock_in_add_items') }}</option>
                        <option value="out" style="font-weight: 700; color: #f1416c;">{{ __('app.stock_out_reduce_items') }}</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">{{ __('app.quantity') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="quantity" class="form-control" style="border-radius: 8px 0 0 8px; padding: 12px 15px; background-color: #f9f9f9;" required min="1">
                        <span class="input-group-text fw-bold" style="border-radius: 0 8px 8px 0; background-color: #f1f1f4; border: none;">{{ $inventory->unit }}</span>
                    </div>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">{{ __('app.remarks_reason_optional') }}</label>
                    <textarea name="remarks" class="form-control" rows="3" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" placeholder="{{ __('app.remarks_reason_optional') }}"></textarea>
                </div>
            </div>

            <hr class="my-4" style="border-color: #eff2f5;">

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold" style="background-color: #009ef7; border: none; padding: 10px 25px; border-radius: 6px;">
                    <i class="fas fa-save me-2"></i> {{ __('app.record_transaction') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
