@extends('layouts.app')

@section('content')
<div class="mb-4 mt-2">
    <a href="{{ route('inventory.index') }}" class="btn btn-sm mb-3 fw-bold" style="background-color: #f1f1f4; color: #7e8299; border-radius: 6px;"><i class="fas fa-arrow-left me-1"></i> Back to Inventory</a>
    <h2 class="fw-bold text-dark" style="font-size: 1.5rem;">Add Inventory Item</h2>
    <p class="text-muted fw-semibold">Register a new item or supply to track in the inventory.</p>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px; max-width: 800px;">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required placeholder="e.g. Glucose Test Kit">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">SKU / Code</label>
                    <input type="text" name="sku" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" placeholder="Optional">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Unit <span class="text-danger">*</span></label>
                    <select name="unit" class="form-select" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Minimum Alert Level <span class="text-danger">*</span></label>
                    <input type="number" name="min_level" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;" required min="0" value="10">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" style="border-radius: 8px; padding: 10px 15px; background-color: #f9f9f9;">
                    <small class="text-muted fw-semibold">Optional for perishables.</small>
                </div>
            </div>

            <hr class="my-4" style="border-color: #eff2f5;">

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold" style="background-color: #009ef7; border: none; padding: 10px 25px; border-radius: 6px;">
                    <i class="fas fa-save me-2"></i> Save Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
