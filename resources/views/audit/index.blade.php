@extends('layouts.app')

@section('content')
<div class="card card-lis">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i> {{ __('app.system_audit_log') ?? 'System Audit Log' }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('app.date_time') }}</th>
                        <th>{{ __('app.user') }}</th>
                        <th>{{ __('app.action') }}</th>
                        <th>{{ __('app.module_target') }}</th>
                        <th>{{ __('app.changes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($activity->causer)
                                <span class="badge bg-secondary">{{ $activity->causer->name }}</span>
                            @else
                                <span class="text-muted">{{ __('app.system') }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = 'bg-info';
                                if($activity->event == 'created') $badgeClass = 'bg-success';
                                elseif($activity->event == 'updated') $badgeClass = 'bg-warning text-dark';
                                elseif($activity->event == 'deleted') $badgeClass = 'bg-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($activity->event) }}</span>
                        </td>
                        <td>
                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                            @if($activity->description && $activity->description !== 'created' && $activity->description !== 'updated')
                                <small class="text-muted d-block">{{ $activity->description }}</small>
                            @endif
                        </td>
                        <td>
                            @if($activity->properties && $activity->properties->count() > 0)
                                <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#activityModal{{ $activity->id }}">{{ __('app.view_details') }}</button>
                                
                                <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('app.activity_details') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <pre class="bg-light p-3 rounded" style="font-size: 0.8rem;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">{{ __('app.no_extra_data') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('app.no_audit_logs_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $activities->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
