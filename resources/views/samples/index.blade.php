@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.samples_title') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.collect_samples') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-lis-success" data-bs-toggle="modal" data-bs-target="#collectSampleModal">
            <i class="fas fa-vial me-1"></i> {{ __('app.collect_samples') }}
        </button>
    </div>
</div>

<div class="card card-lis shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-primary">{{ __('app.samples_title') }}</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('app.sample_id') }}</th>
                        <th>{{ __('app.request_id') }}</th>
                        <th>{{ __('app.patient_name') }}</th>
                        <th>{{ __('app.specimen_types') }}</th>
                        <th>{{ __('app.collected_at') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th class="text-center pe-4">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($samples as $sample)
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#SMP-{{ str_pad($sample->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-bold text-primary">#REQ-{{ str_pad($sample->request->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-bold">{{ $sample->request->patient->name }}</div>
                            <small class="text-muted">{{ $sample->request->patient->patient_code }}</small>
                        </td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 text-uppercase">{{ $sample->sample_type }}</span></td>
                        <td><i class="far fa-clock me-1 text-muted"></i> {{ \Carbon\Carbon::parse($sample->collected_at)->format('M d, H:i') }}</td>
                        <td>
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 border border-success border-opacity-25">
                                <i class="fas fa-check-circle me-1"></i> {{ ucfirst($sample->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('samples.print', $sample) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('app.print') }}" target="_blank">
                                    <i class="fas fa-barcode"></i>
                                </a>
                                <a href="{{ route('samples.show', $sample) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.details') }}">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">{{ __('app.no_samples_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Collect Sample -->
<div class="modal fade" id="collectSampleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('samples.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">{{ __('app.collect_samples') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <i class="fas fa-vials fs-1 text-success opacity-50 mb-3"></i>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">{{ __('app.select') }} {{ __('app.requests') }}</label>
                        <select name="request_id" class="form-select select2-basic" style="width: 100%;" required>
                            <option value="">{{ __('app.select') }}...</option>
                            @foreach(\App\Models\LabRequest::where('status', 'pending')->get() as $req)
                                <option value="{{ $req->id }}">#REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }} - {{ $req->patient->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">{{ __('app.specimen_types') }}</label>
                        <select name="sample_type" class="form-select" required>
                            <option value="blood">Blood (Serum/Plasma)</option>
                            <option value="urine">Urine</option>
                            <option value="swab">Swab</option>
                            <option value="stool">Stool</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">{{ __('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
