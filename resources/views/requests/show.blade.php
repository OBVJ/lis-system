@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-medical"></i> REQ-{{ str_pad($labRequest->id, 5, '0', STR_PAD_LEFT) }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-sm px-3"><i class="fas fa-arrow-left me-1"></i>{{ __('app.back') }}</a>
        @if($labRequest->status == 'completed')
        <a href="{{ route('reports.pdf', $labRequest) }}" class="btn btn-dark btn-sm px-3"><i class="fas fa-file-pdf me-1"></i>{{ __('app.download_report') }}</a>
        @endif
    </div>
</div>

<div class="stat-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-sitemap me-2 text-primary"></i>Workflow Progress</h6>
        <form action="{{ route('requests.update-status', $labRequest) }}" method="POST" class="d-flex align-items-center gap-2">
            @csrf
            @method('PATCH')
            <select name="status" class="form-select form-select-sm" style="width: 150px;">
                <option value="pending" {{ $labRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="collected" {{ in_array($labRequest->status, ['collected', 'sample_collected']) ? 'selected' : '' }}>Collected</option>
                <option value="in_progress" {{ $labRequest->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="review" {{ $labRequest->status == 'review' ? 'selected' : '' }}>Review</option>
                <option value="completed" {{ $labRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="delivered" {{ $labRequest->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
        </form>
    </div>
    <div class="d-flex justify-content-between align-items-start position-relative pb-2" style="border-bottom: 2px solid #f0f0f0;">
        @php
            $stages = [
                'pending' => ['icon' => 'fa-clipboard-list', 'label' => 'Pending', 'at' => $labRequest->created_at],
                'collected' => ['icon' => 'fa-vial', 'label' => 'Collected', 'at' => $labRequest->collected_at],
                'in_progress' => ['icon' => 'fa-flask', 'label' => 'In Progress', 'at' => $labRequest->in_progress_at],
                'review' => ['icon' => 'fa-microscope', 'label' => 'Review', 'at' => $labRequest->review_at],
                'completed' => ['icon' => 'fa-check-circle', 'label' => 'Completed', 'at' => $labRequest->completed_at],
                'delivered' => ['icon' => 'fa-box-open', 'label' => 'Delivered', 'at' => $labRequest->delivered_at],
            ];
            $currentFound = false;
        @endphp
        
        @foreach($stages as $stageKey => $stageData)
            @php
                if($labRequest->status == $stageKey || ($stageKey === 'collected' && $labRequest->status === 'sample_collected')) {
                    $currentFound = true;
                }
                $active = $stageData['at'] !== null || $labRequest->status == $stageKey || ($stageKey === 'collected' && $labRequest->status === 'sample_collected');
            @endphp
            <div class="text-center" style="width: 16%; z-index: 1;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $active ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 40px; height: 40px; border-radius: 50%;">
                    <i class="fas {{ $stageData['icon'] }}"></i>
                </div>
                <div class="mt-2 small fw-bold {{ $active ? 'text-primary' : 'text-muted' }}">{{ $stageData['label'] }}</div>
                @if($stageData['at'])
                    <div class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($stageData['at'])->format('d/m/y H:i') }}</div>
                @endif
            </div>
        @endforeach
        
        <!-- Background line -->
        <div class="position-absolute" style="top: 20px; left: 8%; right: 8%; height: 2px; background-color: #f0f0f0; z-index: 0;"></div>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column -->
    <div class="col-md-4">
        <!-- Patient Info -->
        <div class="stat-card mb-3" style="border-top:3px solid #3498db;">
            <div class="text-center mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:50px;height:50px;">
                    <i class="fas fa-id-card text-primary fa-lg"></i>
                </div>
            </div>
            <h6 class="fw-bold text-center mb-0">{{ $labRequest->patient->name }}</h6>
            <p class="text-muted text-center small fw-bold">{{ $labRequest->patient->patient_code }}</p>
            <hr>
            <p class="mb-1 small"><strong>{{ __('app.age') }}:</strong> {{ $labRequest->patient->age }} years</p>
            <p class="mb-1 small"><strong>{{ __('app.gender') }}:</strong> {{ ucfirst($labRequest->patient->gender) }}</p>
            <p class="mb-0 small"><strong>{{ __('app.phone') }}:</strong> {{ $labRequest->patient->phone ?? '---' }}</p>
        </div>

        <!-- Sample Status -->
        <div class="stat-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-vial me-2 text-primary"></i>{{ __('app.sample_status') }}</h6>
                @if(in_array($labRequest->status, ['pending', 'collected', 'sample_collected']))
                <button type="button" class="btn btn-primary btn-sm px-2" data-bs-toggle="modal" data-bs-target="#collectSampleModal" style="font-size:10px;">
                    <i class="fas fa-plus me-1"></i>Collect
                </button>
                @endif
            </div>
            @forelse($labRequest->samples as $sample)
                <div class="p-2 border rounded mb-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">{{ $sample->sample_type }}</span>
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="text-muted" style="font-size:11px;">
                        <i class="far fa-clock me-1"></i>{{ $sample->collected_at->format('M d, H:i') }}
                    </div>
                    <div class="fw-bold small text-primary mt-1" style="font-size:10px;">
                        <i class="fas fa-barcode me-1"></i>{{ $sample->barcode }}
                    </div>
                </div>
            @empty
                <div class="text-center py-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>No samples collected yet.
                </div>
            @endforelse
        </div>

        <!-- Sample Collection Modal -->
        <div class="modal fade" id="collectSampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form action="{{ route('samples.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $labRequest->id }}">
                        <div class="modal-header py-2">
                            <h6 class="modal-title fw-bold">Collect New Sample</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sample Type</label>
                                <select name="sample_type" class="form-select form-select-sm" required>
                                    <option value="">Select Type...</option>
                                    @foreach(\App\Models\SpecimenType::all() as $type)
                                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer py-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Record Collection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Billing -->
            <h4 class="fw-bold">{{ App\Models\Setting::get('currency_symbol', 'SDG') }} {{ number_format($labRequest->total_price, 2) }}</h4>
            @if($labRequest->payment)
            <span class="badge rounded-pill bg-{{ $labRequest->payment->status == 'paid' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $labRequest->payment->status == 'paid' ? 'success' : 'danger' }} px-3 py-1">
                {{ strtoupper($labRequest->payment->status) }}
            </span>
            @endif
    </div>

    <!-- Right Column: Test Results -->
    <div class="col-md-8">
        <div class="stat-card" style="padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">{{ __('app.test_orders_results') }}</h5>
                <span class="badge-status badge-{{ $labRequest->status }}">
                    {{ strtoupper(str_replace('_', ' ', $labRequest->status)) }}
                </span>
            </div>

            <div class="data-table" style="box-shadow:none;border:1px solid #eee;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.test_name') }}</th>
                            <th>{{ __('app.reference_range') }}</th>
                            <th>{{ __('app.result_value') }}</th>
                            <th>{{ __('app.flag') }}</th>
                            <th class="text-center">{{ __('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labRequest->items as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->test->name }}</td>
                            <td class="text-muted">
                                @if($item->test->normal_min || $item->test->normal_max)
                                {{ $item->test->normal_min }} - {{ $item->test->normal_max }} {{ $item->test->unit }}
                                @else
                                ---
                                @endif
                            </td>
                            <td class="fw-bold">
                                @if($item->result)
                                {{ $item->result->result_value }} {{ $item->test->unit }}
                                @else
                                <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>
                                @if($item->result)
                                <span class="badge-status badge-{{ $item->result->flag }}">{{ strtoupper($item->result->flag) }}</span>
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$item->result)
                                    @if($labRequest->samples()->exists())
                                    <a href="{{ route('results.edit', $labRequest->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill" style="font-size:11px;">
                                        <i class="fas fa-flask me-1"></i> Enter Results
                                    </a>
                                    @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Wait Collection</span>
                                    @endif
                                @else
                                <i class="fas fa-check-circle text-success" title="Completed"></i>
                                @endif
                            </td>
                        </tr>
                        @if($item->result && $item->result->notes)
                        <tr>
                            <td colspan="5" class="text-muted small" style="border:none;padding-top:0;">
                                <i class="fas fa-comment-medical me-1"></i>{{ $item->result->notes }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
