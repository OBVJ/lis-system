@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.test_catalog_title') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.medical_services') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-lis-primary" data-bs-toggle="modal" data-bs-target="#addTestModal">
            <i class="fas fa-plus me-1"></i> {{ __('app.add_new_test') }}
        </button>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form action="{{ route('tests.index') }}" method="GET" class="row g-2">
                    <div class="col-6">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('app.search') }}..." value="{{ request('search') }}">
                    </div>
                    <div class="col-4">
                        <select name="category" class="form-select">
                            <option value="">{{ __('app.all_categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('app.test_id') }}</th>
                        <th>{{ __('app.category') }}</th>
                        <th>{{ __('app.test_name') }}</th>
                        <th>{{ __('app.unit') }}</th>
                        <th>{{ __('app.normal_range') }}</th>
                        <th>{{ __('app.price') }}</th>
                        <th class="text-center">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                    <tr>
                        <td class="ps-4"><span class="text-muted fw-bold">#{{ str_pad($test->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                        <td><span class="badge bg-info bg-opacity-10 text-info px-3">{{ $test->category->name }}</span></td>
                        <td class="fw-bold">{{ $test->name }}</td>
                        <td>{{ $test->unit }}</td>
                        <td><span class="text-success fw-bold">{{ $test->normal_min }} - {{ $test->normal_max }}</span></td>
                        <td class="fw-bold">{{ app_currency($test->price) }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('tests.edit', $test) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.edit') }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('tests.destroy', $test) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('app.are_you_sure') }}');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="{{ __('app.delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">{{ __('app.no_tests_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $tests->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal: Add Test -->
<div class="modal fade" id="addTestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tests.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('app.add_test_to_catalog') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">{{ __('app.test_name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. CBC, Glucose" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">{{ __('app.category') }}</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">{{ __('app.normal_min') }}</label>
                            <input type="number" step="0.01" name="normal_min" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">{{ __('app.normal_max') }}</label>
                            <input type="number" step="0.01" name="normal_max" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">{{ __('app.unit') }}</label>
                            <input type="text" name="unit" class="form-control" placeholder="e.g. mg/dL" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">{{ __('app.price') }} ({{ App\Models\Setting::get('currency_symbol', 'SDG') }})</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('app.save_test') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
