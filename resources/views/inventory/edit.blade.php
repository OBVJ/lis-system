@extends('layouts.app')

@section('content')
<div class="mb-4 mt-2">
    <a href="{{ route('inventory.index') }}" class="btn btn-sm mb-3 fw-bold" style="background-color: #f1f1f4; color: #7e8299; border-radius: 6px;"><i class="fas fa-arrow-left me-1"></i> Back to Inventory</a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark" style="font-size: 1.5rem;">Edit Inventory Item</h2>
            <p class="text-muted fw-semibold">Update details for {{ $inventory->name }}.</p>
        </div>
        <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm fw-bold" style="background-color: #fff5f8; color: #f1416c; border: none; padding: 8px 16px; border-radius: 6px;">
                <i class="fas fa-trash-alt me-2"></i> Delete Item
            </button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 800px;">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('inventory.update', $inventory->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required value="{{ $inventory->name }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">SKU / Code</label>
                    <input type="text" name="sku" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" value="{{ $inventory->sku }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ $inventory->category == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Unit <span class="text-danger">*</span></label>
                    <select name="unit" class="form-select" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required>
                        @foreach($units as $u)
                            <option value="{{ $u->name }}" {{ $inventory->unit == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Min Alert Level <span class="text-danger">*</span></label>
                    <input type="number" name="min_level" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required min="0" value="{{ $inventory->min_level }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" value="{{ $inventory->expiry_date ? $inventory->expiry_date->format('Y-m-d') : '' }}">
                    <small class="text-muted fw-semibold">Update if applicable.</small>
                </div>
            </div>

            <hr class="my-4" style="border-color: #eff2f5;">

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold" style="background-color: #009ef7; border: none; padding: 10px 25px; border-radius: 6px;">
                    <i class="fas fa-save me-2"></i> Update Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
