@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-lg border-0 border-top border-primary border-5 rounded-10">
            <h3 class="mb-4 fw-bold px-2 px-2"><i class="fas fa-edit me-2 text-primary"></i>Edit Test: {{ $test->name }}</h3>
            <form action="{{ route('tests.update', $test->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Test Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" value="{{ $test->name }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Category</label>
                        <select name="category_id" class="form-select form-select-lg rounded shadow-sm border-0 border-bottom border-primary border-2" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $test->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Normal Min Value</label>
                        <input type="number" step="0.01" name="normal_min" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" value="{{ $test->normal_min }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Normal Max Value</label>
                        <input type="number" step="0.01" name="normal_max" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" value="{{ $test->normal_max }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Unit</label>
                        <select name="unit" class="form-select form-select-lg rounded shadow-sm border-0 border-bottom border-primary border-2" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit->name }}" {{ $test->unit == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 px-2">
                        <label class="form-label text-muted">Price ({{ App\Models\Setting::get('currency_symbol', 'SDG') }})</label>
                        <input type="number" step="0.01" name="price" class="form-control form-control-lg rounded shadow-sm border-0 border-bottom border-primary border-2" value="{{ $test->price }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between px-2 mt-4 gap-2">
                    <a href="{{ route('tests.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow"><i class="fas fa-save me-1"></i>Update Test</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
