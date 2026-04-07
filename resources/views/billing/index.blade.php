@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.financial') ?? 'Financial' }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.payment_management') ?? 'Payment Management' }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <div class="row g-2 justify-content-end">
            <div class="col-auto">
                <div class="stat-card-new cyan p-3">
                    <div class="stat-label-new">{{ __('app.revenue_today') ?? 'Revenue Today' }}</div>
                    <div class="stat-value-new">{{ app_currency($revenueToday) }}</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="stat-card-new green p-3">
                    <div class="stat-label-new">{{ __('app.revenue_this_month') ?? 'Revenue This Month' }}</div>
                    <div class="stat-value-new">{{ app_currency($revenueThisMonth) }}</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="stat-card-new yellow p-3">
                    <div class="stat-label-new">{{ __('app.total_unpaid') ?? 'Total Unpaid' }}</div>
                    <div class="stat-value-new">{{ app_currency($totalUnpaid) }}</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="stat-card-new red p-3" style="border-left-color: #f8285a;">
                    <div class="stat-label-new">{{ __('app.refunds_total') ?? 'Refunds Total' }}</div>
                    <div class="stat-value-new">{{ app_currency($refundTotal) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <form action="{{ route('billing.index') }}" method="GET" class="input-group">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if(request('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif
                    <input type="text" name="q" class="form-control" placeholder="{{ __('app.search_payments') ?? 'Search payments...' }}" value="{{ request('q') }}">
                    <button type="submit" class="input-group-text bg-primary text-white border-primary"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="col text-end">
                <div class="btn-group">
                    <a href="{{ route('billing.index', ['status' => 'all'] + request()->except('status')) }}" 
                       class="btn btn-outline-secondary btn-sm {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">{{ __('app.all') }}</a>
                    
                    <a href="{{ route('billing.index', ['status' => 'paid'] + request()->except('status')) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'paid' ? 'active' : '' }}">{{ __('app.paid') }}</a>
                    
                    <a href="{{ route('billing.index', ['status' => 'unpaid'] + request()->except('status')) }}" 
                       class="btn btn-outline-secondary btn-sm {{ request('status') == 'unpaid' ? 'active' : '' }}">{{ __('app.unpaid') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('app.request_id') ?? 'Request ID' }}</th>
                        <th>{{ __('app.patient') ?? 'Patient' }}</th>
                        <th>{{ __('app.amount') ?? 'Amount' }}</th>
                        <th>{{ __('app.status') ?? 'Status' }}</th>
                        <th>{{ __('app.date') ?? 'Date' }}</th>
                        <th>{{ __('app.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">REQ-{{ $payment->request_id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $payment->request->patient->name }}</div>
                            <small class="text-muted">{{ $payment->request->patient->patient_code }}</small>
                        </td>
                        <td>
                            <span class="fw-bold">{{ app_currency($payment->amount) }}</span>
                        </td>
                        <td>
                            @if($payment->status == 'paid')
                                <span class="badge bg-success">{{ __('app.paid') }}</span>
                            @elseif($payment->status == 'refunded')
                                <span class="badge bg-danger">{{ __('app.refunded') ?? 'Refunded' }}</span>
                            @elseif($payment->status == 'partial')
                                <span class="badge bg-warning text-dark">{{ __('app.partial') ?? 'Partial' }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('app.unpaid') }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $payment->created_at->format('M d, Y') }}
                            @if($payment->paid_at)
                                <br><small class="text-success">{{ __('app.paid_on') ?? 'Paid on' }}: {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('billing.receipt', $payment) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                    <i class="fas fa-receipt"></i> {{ __('app.receipt') ?? 'Receipt' }}
                                </a>
                                <a href="{{ route('billing.invoice', $payment) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-file-pdf"></i> {{ __('app.invoice') ?? 'Invoice' }}
                                </a>
                                @if($payment->status == 'unpaid')
                                <form action="{{ route('billing.mark-paid', $payment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> {{ __('app.mark_paid') ?? 'Mark Paid' }}
                                    </button>
                                </form>
                                @endif
                                @if($payment->status !== 'refunded' && $payment->paid_amount > 0)
                                <form action="{{ route('billing.refund', $payment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-undo"></i> {{ __('app.refund') ?? 'Refund' }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <div class="text-muted">{{ __('app.no_payments_found') ?? 'No payments found' }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $payments->appends(request()->query())->links() }}
    </div>
</div>
@endsection