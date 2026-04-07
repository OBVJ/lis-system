@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.5rem;">{{ __('app.inventory_title') }}</h2>
        <p class="text-muted mb-0 fw-semibold">{{ __('app.inventory_title') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('settings.inventory-categories.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 6px; padding: 10px 20px;">
            <i class="fas fa-layer-group me-2"></i> {{ __('app.inventory_categories') }}
        </a>
        <a href="{{ route('settings.inventory-units.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 6px; padding: 10px 20px;">
            <i class="fas fa-boxes me-2"></i> {{ __('app.inventory_units') }}
        </a>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary fw-bold" style="background-color: #009ef7; border: none; padding: 10px 20px; border-radius: 6px;">
            <i class="fas fa-plus-circle me-2"></i> {{ __('app.add_item') }}
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="fw-bold px-4 py-3 border-bottom text-uppercase" style="font-size: 0.8rem;">{{ __('app.item_name') }}</th>
                        <th class="fw-bold px-4 py-3 border-bottom text-uppercase" style="font-size: 0.8rem;">{{ __('app.category') }}</th>
                        <th class="fw-bold px-4 py-3 border-bottom text-uppercase" style="font-size: 0.8rem;">{{ __('app.quantity') }}</th>
                        <th class="fw-bold px-4 py-3 border-bottom text-uppercase" style="font-size: 0.8rem;">{{ __('app.status') }}</th>
                        <th class="fw-bold px-4 py-3 border-bottom text-uppercase text-end" style="font-size: 0.8rem;">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3 border-bottom">
                                <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                <div class="text-muted small fw-semibold">{{ $item->sku ?? '---' }}</div>
                            </td>
                            <td class="px-4 py-3 border-bottom">
                                <span class="badge fw-bold" style="background-color: #f1f1f4; color: #7e8299; border-radius: 6px; padding: 5px 10px;">{{ $item->category }}</span>
                            </td>
                            <td class="px-4 py-3 border-bottom fw-bold fs-5 text-dark">
                                {{ $item->current_stock }} <span class="fs-6 text-muted fw-semibold ms-1">{{ $item->unit }}</span>
                            </td>
                            <td class="px-4 py-3 border-bottom">
                                @if($item->current_stock <= 0)
                                    <span class="badge" style="background-color: #fff5f8; color: #f1416c; padding: 6px 12px; border-radius: 6px; font-weight: 700;"><i class="fas fa-exclamation-triangle me-1"></i> {{ __('app.out_of_stock') }}</span>
                                @elseif($item->current_stock <= $item->min_level)
                                    <span class="badge" style="background-color: #fff8dd; color: #ffc700; padding: 6px 12px; border-radius: 6px; font-weight: 700;"><i class="fas fa-exclamation-circle me-1"></i> {{ __('app.low_stock') }}</span>
                                @else
                                    <span class="badge" style="background-color: #e8fff3; color: #50cd89; padding: 6px 12px; border-radius: 6px; font-weight: 700;"><i class="fas fa-check-circle me-1"></i> {{ __('app.in_stock') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-bottom text-end">
                                <a href="{{ route('inventory.transaction', $item->id) }}" class="btn btn-sm fw-bold me-1" style="background-color: #f1f1f4; color: #009ef7; border-radius: 6px; padding: 6px 12px;"><i class="fas fa-exchange-alt me-1"></i> {{ __('app.add_transaction') }}</a>
                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-light fw-bold" style="border-radius: 6px; color: #7e8299;"><i class="fas fa-edit"></i> {{ __('app.edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted fw-bold">
                                <i class="fas fa-box-open fs-1 mb-3 text-light"></i><br>
                                {{ __('app.no_inventory_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
