<div class="list-group list-group-custom shadow-sm border-0 mb-4">
    <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.index') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-cogs me-3 fs-5"></i> {{ __('app.general_config') }}
    </a>
    <a href="{{ route('settings.test-categories.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.test-categories.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-vials me-3 fs-5"></i> {{ __('app.test_categories') }}
    </a>
    <a href="{{ route('settings.specimen-types.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.specimen-types.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-vial me-3 fs-5"></i> {{ __('app.specimen_types') }}
    </a>
    <a href="{{ route('settings.lab-units.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.lab-units.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-flask me-3 fs-5"></i> {{ __('app.lab_units') }}
    </a>
    <hr class="my-0 opacity-25">
    <a href="{{ route('settings.inventory-categories.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.inventory-categories.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-layer-group me-3 fs-5"></i> {{ __('app.inventory_categories') }}
    </a>
    <a href="{{ route('settings.inventory-units.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('settings.inventory-units.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-boxes me-3 fs-5"></i> {{ __('app.inventory_units') }}
    </a>
    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('users.*') ? 'active' : 'text-muted' }} d-flex align-items-center fw-bold py-3">
        <i class="fas fa-users-cog me-3 fs-5"></i> {{ __('app.manage_users') }}
    </a>
</div>
