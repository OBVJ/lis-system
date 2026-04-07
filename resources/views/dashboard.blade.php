@extends('layouts.app')

@section('content')
<style>
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .dashboard-title { font-size: 1.5rem; font-weight: 800; color: #181c32; }
    .header-btns .btn { font-weight: 700; border-radius: 6px; padding: 8px 16px; font-size: 0.9rem; }
    .btn-new-patient { background-color: #009ef7; border: none; color: #fff; }
    .btn-new-request { background-color: #50cd89; border: none; color: #fff; }

    .alert-banner { border-radius: 8px; padding: 8px 16px; display: inline-flex; align-items: center; justify-content: space-between; font-weight: 700; font-size: 0.9rem; }
    .alert-delayed { background-color: #fff8e1; border: 1px solid #ffe082; color: #181c32; }
    .alert-abnormal { background-color: #f8285a; color: #fff; border: 1px solid #f8285a; }
    .alert-banner i { font-size: 1.1rem; margin-right: 8px; }
    [dir="rtl"] .alert-banner i { margin-right: 0; margin-left: 8px; }
    .alert-delayed i { color: #856404; }
    .btn-check-lis { background: #ffc107; border: none; font-size: 0.8rem; font-weight: 700; padding: 4px 15px; border-radius: 6px; color: #181c32; }

    .stat-card-new { background: #fff; border-radius: 12px; padding: 25px; border-left: 6px solid; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; height: 100%; border-color: transparent;}
    [dir="rtl"] .stat-card-new { border-left: none; border-right: 6px solid transparent; }
    .stat-card-new.blue { border-left-color: #009ef7; }
    [dir="rtl"] .stat-card-new.blue { border-left-color: transparent; border-right-color: #009ef7; }
    .stat-card-new.green { border-left-color: #50cd89; }
    [dir="rtl"] .stat-card-new.green { border-left-color: transparent; border-right-color: #50cd89; }
    .stat-card-new.yellow { border-left-color: #ffc700; }
    [dir="rtl"] .stat-card-new.yellow { border-left-color: transparent; border-right-color: #ffc700; }
    .stat-card-new.cyan { border-left-color: #00d2ff; }
    [dir="rtl"] .stat-card-new.cyan { border-left-color: transparent; border-right-color: #00d2ff; }
    
    .stat-label-new { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
    .stat-card-new.blue .stat-label-new { color: #009ef7; }
    .stat-card-new.green .stat-label-new { color: #50cd89; }
    .stat-card-new.yellow .stat-label-new { color: #ffc700; }
    .stat-card-new.cyan .stat-label-new { color: #00d2ff; }
    
    .stat-value-new { font-size: 2rem; font-weight: 800; color: #181c32; line-height: 1; }
    .stat-icon-new { color: #a1a5b7; font-size: 2.2rem; }

    .prediction-card { border-radius: 12px; padding: 25px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: 100%; }
    .risk-card { border-radius: 12px; padding: 25px; background: #f8285a; color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: 100%; }
    .prediction-value { font-size: 2.8rem; font-weight: 800; line-height: 1; margin-bottom: 0.5rem; }
    
    .test-list-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background: #f9fafb; border-radius: 8px; margin-bottom: 8px; font-weight: 700; font-size: 0.9rem; color: #181c32;}
    .test-badge { background: #7e8299; color: #fff; border-radius: 50rem; font-size: 0.75rem; padding: 4px 12px; font-weight: 600; }

    .chart-container-lis { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 20px; }
    .section-title { font-weight: 800; font-size: 1rem; color: #009ef7; margin-bottom: 1.5rem; display: flex; align-items: center; }
    .section-title i { margin-right: 8px; }
    [dir="rtl"] .section-title i { margin-right: 0; margin-left: 8px; }
</style>

<div class="dashboard-header mt-2">
    <h1 class="dashboard-title mb-0">{{ __('app.lab_dashboard') }}</h1>
    <div class="header-btns d-flex gap-2">
        <a href="{{ route('patients.create') }}" class="btn btn-lis-primary d-flex align-items-center">
            <i class="fas fa-user-plus me-2"></i> {{ __('app.new_patient') ?? 'New Patient' }}
        </a>
        <a href="{{ route('queue') }}" class="btn btn-lis-secondary d-flex align-items-center">
            <i class="fas fa-vial me-2"></i> {{ __('app.collect_sample') ?? 'Collect Sample' }}
        </a>
        <a href="{{ route('results.index') }}" class="btn btn-outline-primary d-flex align-items-center">
            <i class="fas fa-flask me-2"></i> {{ __('app.enter_results') ?? 'Enter Results' }}
        </a>
    </div>
</div>

<div class="d-flex flex-wrap gap-3 mb-4">
    <div class="alert-banner alert-delayed">
        <span><i class="far fa-clock"></i> {{ __('app.delayed_requests') }}: {{ $delayedRequestsCount }} {{ __('app.older_than_48h') }}</span>
        <a href="{{ route('requests.index', ['status' => 'pending']) }}" class="btn btn-check-lis ms-3 text-decoration-none">{{ __('app.check') }}</a>
    </div>
    <div class="alert-banner alert-abnormal">
        <span><i class="fas fa-heartbeat"></i> {{ __('app.abnormal_results_today') }}: {{ $abnormalResultsTodayCount }}</span>
    </div>
    @if($lowStockCount > 0)
    <div class="alert-banner" style="background-color: #fff5f8; border: 1px solid #f1416c; color: #f1416c;">
        <span><i class="fas fa-exclamation-triangle"></i> {{ __('app.low_stock_alerts') }}: {{ $lowStockCount }} {{ __('app.items_need_attention') }}</span>
        <a href="{{ route('inventory.index') }}" class="btn btn-sm ms-3" style="background: #f1416c; color: #fff; font-size: 0.7rem;">{{ __('app.restock') }}</a>
    </div>
    @endif
    @if($expiringSoonCount > 0)
    <div class="alert-banner" style="background-color: #fff8e1; border: 1px solid #ffc107; color: #856404;">
        <span><i class="fas fa-hourglass-end"></i> {{ __('app.expiry_warning') }}: {{ $expiringSoonCount }} {{ __('app.items_expiring_30_days') }}</span>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card-new blue">
            <div>
                <div class="stat-label-new">{{ __('app.total_requests') }}</div>
                <div class="stat-value-new">{{ $stats['total_requests'] }}</div>
            </div>
            <div class="stat-icon-new"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new green">
            <div>
                <div class="stat-label-new">{{ __('app.completed_today') }}</div>
                <div class="stat-value-new">{{ $stats['completed_today'] }}</div>
            </div>
            <div class="stat-icon-new"><i class="far fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new yellow">
            <div>
                <div class="stat-label-new">{{ __('app.pending_in_progress') }}</div>
                <div class="stat-value-new">{{ $stats['pending_requests'] }}</div>
            </div>
            <div class="stat-icon-new"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new cyan">
            <div>
                <div class="stat-label-new">{{ __('app.revenue_today') }}</div>
                <div class="stat-value-new">{{ app_currency($stats['revenue_today']) }}</div>
            </div>
            <div class="stat-icon-new"><i class="fas fa-coins"></i></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="prediction-card h-100">
            <h6 class="section-title"><i class="fas fa-magic me-2"></i> {{ __('app.smart_predictions') }}</h6>
            <div class="mb-3">
                <div class="text-muted small fw-bold mb-1">{{ __('app.expected_volume_next_month') }}</div>
                <div class="prediction-value text-dark" style="font-size: 2.2rem;">{{ $prediction['expected_next_month'] }} <span class="badge {{ $prediction['growth'] >= 0 ? 'bg-success' : 'bg-danger' }} border-0" style="font-size: 0.8rem; vertical-align: middle;"><i class="fas {{ $prediction['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> {{ abs($prediction['growth']) }}%</span></div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle mb-0">
                    <thead class="text-muted small border-bottom">
                        <tr>
                            <th>{{ __('app.test') }}</th>
                            <th>{{ __('app.trend') }}</th>
                            <th class="text-end">{{ __('app.prediction') }}</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.85rem; font-weight: 600;">
                        @forelse($detailedPredictions as $pred)
                        <tr>
                            <td>{{ Str::limit($pred['test_name'], 15) }}</td>
                            <td>
                                @if($pred['trend_percent'] > 0)
                                <span class="text-success"><i class="fas fa-caret-up"></i> {{ $pred['trend_percent'] }}%</span>
                                @elseif($pred['trend_percent'] < 0)
                                <span class="text-danger"><i class="fas fa-caret-down"></i> {{ abs($pred['trend_percent']) }}%</span>
                                @else
                                <span class="text-muted"><i class="fas fa-minus"></i> 0%</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $pred['prediction_next_month'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-2">{{ __('app.no_enough_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="risk-card h-100 position-relative overflow-hidden">
            <div class="position-absolute" style="top: -20px; right: -20px; font-size: 8rem; opacity: 0.1;"><i class="fas fa-heartbeat"></i></div>
            <h6 class="text-white fw-bold mb-3 d-flex align-items-center position-relative" style="font-size: 1rem; z-index: 1;"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('app.clinical_risk_analysis') }}</h6>
            <div class="mb-3 position-relative" style="z-index: 1;">
                <div class="prediction-value" style="font-size: 2.2rem;">{{ number_format($abnormalRate, 1) }}% <span class="fs-6 fw-normal">{{ __('app.overall_abnormal') }}</span></div>
            </div>
            <div class="position-relative" style="z-index: 1;">
                <div class="small text-white-50 mb-2 text-uppercase fw-bold">{{ __('app.test_specific_abnormal_rates') }}</div>
                @forelse($detailedPredictions as $pred)
                    @if($pred['abnormal_rate'] > 0)
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom border-light border-opacity-25" style="font-size: 0.85rem;">
                        <span>{{ Str::limit($pred['test_name'], 15) }}</span>
                        <span class="fw-bold">{{ number_format($pred['abnormal_rate'], 1) }}%</span>
                    </div>
                    @endif
                @empty
                    <div class="small">{{ __('app.no_abnormal_rates_found') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="prediction-card h-100" style="background-color: #f1faff; border: 1px solid #cbe9ff;">
            <h6 class="section-title text-primary"><i class="fas fa-lightbulb"></i> {{ __('app.automated_insights') }}</h6>
            <div class="mt-3">
                @foreach($insights as $insight)
                <div class="d-flex align-items-start mb-3 p-3 bg-white rounded shadow-sm border border-primary border-opacity-25">
                    <div class="bg-primary bg-opacity-10 text-primary rounded px-2 py-1 me-3"><i class="fas fa-robot"></i></div>
                    <div class="small fw-bold text-dark" style="line-height: 1.4;">{{ $insight }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Manager Analytics Section --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="chart-container-lis">
            <h6 class="section-title"><i class="fas fa-star"></i> {{ __('app.top_requested_tests_30_days') }}</h6>
            <div class="mt-3">
                @forelse($topTests ?? [] as $test)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <span class="fw-bold text-dark">{{ Str::limit($test->name, 20) }}</span>
                    <span class="badge bg-primary">{{ $test->count }}</span>
                </div>
                @empty
                <div class="text-muted small">{{ __('app.no_test_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-container-lis">
            <h6 class="section-title"><i class="fas fa-calendar-week"></i> {{ __('app.peak_days_analysis') }}</h6>
            <div class="mt-3">
                @forelse($peakDays ?? [] as $day)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <span class="fw-bold text-dark">{{ $day->day_name }}</span>
                    <span class="badge bg-success">{{ $day->count }}</span>
                </div>
                @empty
                <div class="text-muted small">{{ __('app.no_peak_day_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-container-lis">
            <h6 class="section-title"><i class="fas fa-box-open"></i> {{ __('app.inventory_consumption_30_days') }}</h6>
            <div class="mt-3">
                @forelse($inventoryConsumption ?? [] as $item)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <span class="fw-bold text-dark">Item #{{ $item->inventory_item_id }}</span>
                    <span class="badge bg-warning">{{ $item->total_used }}</span>
                </div>
                @empty
                <div class="text-muted small">{{ __('app.no_consumption_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="chart-container-lis">
            <h6 class="section-title">{{ __('app.ops_revenue_trend') }}</h6>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="opsTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-container-lis">
            <h6 class="section-title">{{ __('app.test_distribution') }}</h6>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="testDistChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const ctxTrend = document.getElementById('opsTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [
                    {
                        label: '{{ __("app.revenue_today") }} ({{ App\Models\Setting::get("currency_symbol", "SDG") }})',
                        data: {!! json_encode($revenueCounts) !!},
                        borderColor: '#00d2ff',
                        backgroundColor: 'rgba(0, 210, 255, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        yAxisID: 'y_rev'
                    },
                    {
                        label: '{{ __("app.requests_label") }}',
                        data: {!! json_encode($requestCounts) !!},
                        borderColor: '#7239ea',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 3,
                        yAxisID: 'y_req'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y_rev: {
                        beginAtZero: true,
                        position: '{{ app()->getLocale() == "ar" ? "right" : "left" }}',
                        grid: { borderDash: [2, 2], drawBorder: false },
                        ticks: { callback: (v) => v.toLocaleString() + ' {{ App\Models\Setting::get("currency_symbol", "SDG") }}' }
                    },
                    y_req: {
                        beginAtZero: true,
                        position: '{{ app()->getLocale() == "ar" ? "left" : "right" }}',
                        grid: { display: false }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 10 } }
                }
            }
        });

        const ctxDist = document.getElementById('testDistChart').getContext('2d');
        new Chart(ctxDist, {
            type: 'bar',
            data: {
                labels: {!! json_encode($distLabels) !!},
                datasets: [{
                    data: {!! json_encode($distValues) !!},
                    backgroundColor: '#7239ea',
                    borderRadius: 5,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
@endsection
