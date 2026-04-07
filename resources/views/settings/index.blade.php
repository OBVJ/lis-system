@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0 text-uppercase small text-muted">{{ __('app.admin') }}</h3>
        <h2 class="fw-bold mb-0">{{ __('app.settings_title') }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        @include('settings._sidebar')
    </div>
    
    <div class="col-md-9">
        <div class="card card-lis shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="fw-bold text-primary mb-0"><i class="fas fa-money-bill-wave me-2"></i> {{ __('app.currency') }}</h5>
            </div>
            <div class="card-body bg-light border-top p-4">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="currency_symbol" class="form-label fw-bold text-dark">{{ __('app.currency_symbol') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-coins text-warning"></i></span>
                            <input type="text" 
                                   class="form-control form-control-lg fw-bold" 
                                   id="currency_symbol" 
                                   name="currency_symbol" 
                                   value="{{ App\Models\Setting::get('currency_symbol', 'SDG') }}" 
                                   placeholder="e.g. SDG, $, EUR">
                        </div>
                    </div>
                    
                    <hr class="text-muted opacity-25 my-4">
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> {{ __('app.save_settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
