<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LIS - {{ __('app.lab_dashboard') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { 
            --lis-dark:#1e1e2d; 
            --lis-blue:#009ef7; 
            --lis-light-bg:#f5f8fa; 
            --lis-text:#181c32;
            --lis-success:#50cd89;
            --lis-warning:#ffc700;
            --lis-danger:#f8285a;
            --lis-info:#00d2ff;
            --lis-secondary:#6c757d;
            --lis-border:#e1e3ea;
            --lis-card-bg:#ffffff;
            --lis-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background-color: var(--lis-light-bg);
            color: var(--lis-text);
        }

        /* Standardized Components */
        .lis-card {
            background: var(--lis-card-bg);
            border-radius: 12px;
            box-shadow: var(--lis-shadow);
            border: 1px solid var(--lis-border);
        }

        .lis-btn-primary {
            background-color: var(--lis-blue);
            border-color: var(--lis-blue);
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .lis-btn-primary:hover {
            background-color: #007acc;
            border-color: #007acc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,158,247,0.3);
        }

        .lis-btn-secondary {
            background-color: var(--lis-secondary);
            border-color: var(--lis-secondary);
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .lis-input {
            border: 2px solid var(--lis-border);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }

        .lis-input:focus {
            border-color: var(--lis-blue);
            box-shadow: 0 0 0 3px rgba(0,158,247,0.1);
            outline: none;
        }

        .lis-select {
            border: 2px solid var(--lis-border);
            border-radius: 8px;
            padding: 12px 16px;
            background: white;
            min-height: 48px;
        }

        .lis-select:focus {
            border-color: var(--lis-blue);
            box-shadow: 0 0 0 3px rgba(0,158,247,0.1);
        }

        .lis-table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--lis-shadow);
        }

        .lis-table th {
            background: var(--lis-dark);
            color: white;
            font-weight: 600;
            padding: 15px;
            border: none;
        }

        .lis-table td {
            padding: 15px;
            border-bottom: 1px solid var(--lis-border);
        }

        .lis-badge-primary {
            background: var(--lis-blue);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .lis-section-title {
            color: var(--lis-blue);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .lis-section-title i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        [dir="rtl"] .lis-section-title i {
            margin-right: 0;
            margin-left: 8px;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background-color: var(--lis-light-bg);
            color: var(--lis-text);
        }

        .navbar-lis { background-color:var(--lis-dark); padding:0; height:65px; box-shadow:0 4px 10px rgba(0,0,0,.05); }
        .navbar-brand-lis { font-weight:800; font-size:1.25rem; color:#fff!important; display:flex; align-items:center; padding-left:20px; height:100%; }
        [dir="rtl"] .navbar-brand-lis { padding-left: 0; padding-right: 20px; }
        .brand-icon { color:#f8285a; margin-right:12px; font-size:1.3rem; }
        [dir="rtl"] .brand-icon { margin-right:0; margin-left:12px; }

        .search-container { position:relative; margin-left:30px; width:380px; }
        [dir="rtl"] .search-container { margin-left:0; margin-right:30px; }
        .search-bar { background:#2b2b40; border:1px solid #2b2b40; border-radius:6px; color:#fff; padding:9px 15px 9px 38px; width:100%; font-size:.85rem; transition:all .3s; }
        [dir="rtl"] .search-bar { padding:9px 38px 9px 15px; }
        .search-bar:focus { background:#323248; outline:none; border-color:#323248; }
        .search-bar::placeholder { color:#5e6278; font-weight:500; }
        .search-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#009ef7; font-size:.9rem; }
        [dir="rtl"] .search-icon { left:auto; right:14px; }

        .nav-link-lis { color:#a1a5b7!important; font-size:.88rem; font-weight:600; padding:23px 14px!important; border-bottom:2px solid transparent; white-space:nowrap; }
        .nav-link-lis:hover, .nav-link-lis.active { color:#fff!important; background-color:rgba(255,255,255,.03); }
        .nav-item-icon { margin-right:5px; font-size:.9rem; }
        [dir="rtl"] .nav-item-icon { margin-right:0; margin-left:5px; }

        /* Admin badge on settings link */
        .admin-nav-link { border-bottom:2px solid #f8285a!important; }
        .admin-nav-link:hover, .admin-nav-link.active { border-bottom-color:#f8285a!important; color:#f8285a!important; }

        .user-dropdown-btn { background:rgba(255,255,255,.05); border-radius:6px; padding:8px 14px; color:#fff; font-size:.85rem; font-weight:600; display:flex; align-items:center; gap:8px; text-decoration:none; transition:background .3s; }
        .user-dropdown-btn:hover { color:#fff; background:rgba(255,255,255,.1); }
        .user-dropdown-btn .avatar { width:32px; height:32px; border-radius:50%; background:#009ef7; display:flex; align-items:center; justify-content:center; font-size:.85rem; font-weight:700; color:#fff; flex-shrink:0; }

        /* Lang switcher button */
        .lang-switcher-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; color: #fff !important; padding: 6px 12px !important; font-size: .82rem !important; font-weight: 700 !important; transition: background .2s; }
        .lang-switcher-btn:hover { background: rgba(255,255,255,0.2) !important; color: #fff !important; }

        /* Settings sidebar styles */
        .list-group-custom .list-group-item { border-left:4px solid transparent; border-radius:0!important; transition:all .2s; }
        [dir="rtl"] .list-group-custom .list-group-item { border-left:none; border-right:4px solid transparent; }
        .list-group-custom .list-group-item.active { background-color:rgba(0,158,247,.1); color:#009ef7; border-left-color:#009ef7!important; }
        [dir="rtl"] .list-group-custom .list-group-item.active { border-left-color:transparent!important; border-right-color:#009ef7!important; }
        .list-group-custom .list-group-item:hover { background-color:rgba(0,158,247,.05); color:#009ef7; }

        /* Card utility */
        .card-lis { border:0; box-shadow:0 4px 15px rgba(0,0,0,.02); border-radius:12px; }

        /* Btn lis primary */
        .btn-lis-primary { background-color: #009ef7; border: none; color: #fff; font-weight: 600; }
        .btn-lis-primary:hover { background-color: #0087d4; color: #fff; }

        /* Flash messages */
        .alert-flash { position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn .3s ease; }
        [dir="rtl"] .alert-flash { right: auto; left: 20px; }
        @keyframes slideIn { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }
        [dir="rtl"] @keyframes slideIn { from { opacity:0; transform:translateX(-30px); } to { opacity:1; transform:translateX(0); } }
    </style>
</head>
<body>

<!-- DEMO MODE BANNER -->
<div class="bg-warning text-dark text-center py-2 fw-bold" style="font-size: 0.85rem; letter-spacing: 0.5px; border-bottom: 2px solid #e0a800;">
    <i class="fas fa-flask"></i> {{ __('app.demo_mode_banner') }}
</div>

<nav class="navbar navbar-expand-lg navbar-lis">
    <div class="container-fluid px-0 h-100">
        <div class="d-flex align-items-center h-100">
            <a class="navbar-brand navbar-brand-lis" href="{{ route('dashboard') }}">
                <i class="fas fa-heartbeat brand-icon"></i> LIS
            </a>
            <div class="search-container d-none d-xl-block">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-bar" placeholder="{{ __('app.search') }}...">
            </div>
        </div>

        <button class="navbar-toggler text-white me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse px-3 h-100" id="navContent">
            <ul class="navbar-nav ms-auto align-items-center h-100 gap-1">

                {{-- Language Switcher --}}
                <li class="nav-item me-1">
                    @if(app()->getLocale() == 'ar')
                        <a class="nav-link lang-switcher-btn d-flex align-items-center gap-1" href="{{ route('lang.switch', 'en') }}">
                            <i class="fas fa-globe"></i> EN
                        </a>
                    @else
                        <a class="nav-link lang-switcher-btn d-flex align-items-center gap-1" href="{{ route('lang.switch', 'ar') }}">
                            <i class="fas fa-globe"></i> عربي
                        </a>
                    @endif
                </li>

                {{-- Main nav links --}}
                @can('view_dashboard')
                <li class="nav-item">
                    <a class="nav-link nav-link-lis {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt nav-item-icon"></i> {{ __('app.dashboard') }}
                    </a>
                </li>
                @endcan

                {{-- Operations Module --}}
                @canany(['manage_patients', 'manage_requests'])
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-lis dropdown-toggle {{ request()->routeIs('patients.*') || request()->routeIs('requests.*') || request()->routeIs('queue') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-stethoscope nav-item-icon"></i> {{ __('app.operations') ?? 'Operations' }}
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2">
                        <li><a class="dropdown-item fw-semibold" href="{{ route('queue') }}"><i class="fas fa-clipboard-list me-2 text-primary"></i>{{ __('app.queue') }}</a></li>
                        @can('manage_patients')<li><a class="dropdown-item fw-semibold" href="{{ route('patients.index') }}"><i class="fas fa-users me-2 text-success"></i>{{ __('app.patients') }}</a></li>@endcan
                        @can('manage_requests')<li><a class="dropdown-item fw-semibold" href="{{ route('requests.index') }}"><i class="fas fa-file-invoice me-2 text-info"></i>{{ __('app.requests') }}</a></li>@endcan
                    </ul>
                </li>
                @endcanany

                {{-- Laboratory Module --}}
                @canany(['manage_samples', 'manage_results'])
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-lis dropdown-toggle {{ request()->routeIs('samples.*') || request()->routeIs('results.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-flask nav-item-icon"></i> {{ __('app.laboratory') ?? 'Laboratory' }}
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2">
                        @can('manage_samples')<li><a class="dropdown-item fw-semibold" href="{{ route('samples.index') }}"><i class="fas fa-vial me-2 text-warning"></i>{{ __('app.samples') }}</a></li>@endcan
                        @can('manage_results')<li><a class="dropdown-item fw-semibold" href="{{ route('results.index') }}"><i class="fas fa-microscope me-2 text-danger"></i>{{ __('app.results') }}</a></li>@endcan
                    </ul>
                </li>
                @endcanany

                {{-- Financial Module --}}
                @can('manage_requests')
                <li class="nav-item">
                    <a class="nav-link nav-link-lis {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                        <i class="fas fa-coins nav-item-icon"></i> {{ __('app.financial') ?? 'Financial' }}
                    </a>
                </li>
                @endcan

                {{-- Inventory Module --}}
                @can('manage_inventory')
                <li class="nav-item">
                    <a class="nav-link nav-link-lis {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                        <i class="fas fa-boxes nav-item-icon"></i> {{ __('app.inventory') }}
                    </a>
                </li>
                @endcan

                {{-- Reports Module --}}
                @can('view_reports')
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-lis dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-chart-bar nav-item-icon"></i> {{ __('app.reports') }}
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2">
                        <li><a class="dropdown-item fw-semibold" href="{{ route('reports.operational') ?? '#' }}"><i class="fas fa-chart-line me-2 text-primary"></i>{{ __('app.operational_reports') ?? 'Operational' }}</a></li>
                        <li><a class="dropdown-item fw-semibold" href="{{ route('reports.financial') ?? '#' }}"><i class="fas fa-dollar-sign me-2 text-success"></i>{{ __('app.financial_reports') ?? 'Financial' }}</a></li>
                        <li><a class="dropdown-item fw-semibold" href="{{ route('reports.medical') ?? '#' }}"><i class="fas fa-heartbeat me-2 text-danger"></i>{{ __('app.medical_insights') ?? 'Medical Insights' }}</a></li>
                    </ul>
                </li>
                @endcan
                {{-- Test Catalog (if not in lab module) --}}
                @can('manage_tests')
                <li class="nav-item">
                    <a class="nav-link nav-link-lis {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">
                        <i class="fas fa-flask nav-item-icon"></i> {{ __('app.test_catalog') }}
                    </a>
                </li>
                @endcan

                {{-- Admin-only: Settings --}}
                @auth
                    @canany(['manage_settings', 'manage_users', 'manage_roles'])
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-lis admin-nav-link dropdown-toggle {{ request()->routeIs('settings.*') || request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-shield-alt nav-item-icon"></i> {{ __('app.admin') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:220px;">
                            @can('manage_settings')
                            <li><h6 class="dropdown-header text-uppercase fw-bold" style="font-size:.7rem; letter-spacing:.05em;">{{ __('app.settings') }}</h6></li>
                            <li><a class="dropdown-item fw-semibold" href="{{ route('settings.index') }}"><i class="fas fa-cogs me-2 text-muted"></i>{{ __('app.general_config') }}</a></li>
                            <li><a class="dropdown-item fw-semibold" href="{{ route('audit.index') }}"><i class="fas fa-history me-2 text-muted"></i>{{ __('app.audit_log') ?? 'Audit Logs' }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @endcan
                            @canany(['manage_users', 'manage_roles'])
                            <li><h6 class="dropdown-header text-uppercase fw-bold" style="font-size:.7rem; letter-spacing:.05em;">{{ __('app.manage_users') }} & {{ __('app.manage_roles') }}</h6></li>
                            @can('manage_users')<li><a class="dropdown-item fw-semibold" href="{{ route('users.index') }}"><i class="fas fa-users-cog me-2 text-muted"></i>{{ __('app.manage_users') }}</a></li>@endcan
                            @can('manage_roles')<li><a class="dropdown-item fw-semibold" href="{{ route('roles.index') }}"><i class="fas fa-user-shield me-2 text-muted"></i>{{ __('app.manage_roles') }}</a></li>@endcan
                            @endcanany
                        </ul>
                    </li>
                    @endcanany
                @endauth

                {{-- User Dropdown --}}
                <li class="nav-item ms-2">
                    <div class="dropdown">
                        <a class="user-dropdown-btn dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                            <span class="d-none d-lg-block">{{ Auth::user()->name ?? 'User' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="min-width:200px;">
                            <li class="px-3 py-2">
                                <div class="fw-bold text-dark">{{ Auth::user()->name ?? '' }}</div>
                                @auth
                                    @php $role = Auth::user()->getRoleNames()->first() @endphp
                                    <small class="text-muted">{{ ucwords(str_replace('_',' ',$role ?? 'User')) }}</small>
                                @endauth
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item fw-semibold text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('app.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible alert-flash shadow" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible alert-flash shadow" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="container-fluid px-4 py-3">
    @if(!request()->routeIs('dashboard'))
    <div class="mb-3">
        <button onclick="window.history.back()" class="btn btn-sm btn-outline-secondary px-3 shadow-sm rounded-pill fw-bold">
            <i class="fas fa-arrow-left me-1"></i> {{ __('app.back') }}
        </button>
    </div>
    @endif
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    // Auto-dismiss flash messages after 4s
    setTimeout(function() {
        document.querySelectorAll('.alert-flash').forEach(function(el) {
            var alert = new bootstrap.Alert(el);
            alert.close();
        });
    }, 4000);

    // Keyboard shortcuts for productivity
    document.addEventListener('keydown', function(e) {
        // Ctrl+N: New Patient
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = '{{ route("patients.create") }}';
        }
        // Ctrl+R: New Request
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            window.location.href = '{{ route("requests.create") }}';
        }
        // Ctrl+Q: Queue
        if (e.ctrlKey && e.key === 'q') {
            e.preventDefault();
            window.location.href = '{{ route("queue") }}';
        }
        // Ctrl+D: Dashboard
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            window.location.href = '{{ route("dashboard") }}';
        }
        // Escape: Go back
        if (e.key === 'Escape' && !e.ctrlKey) {
            if (window.history.length > 1) {
                window.history.back();
            }
        }
    });

    // Auto-focus first input field
    document.addEventListener('DOMContentLoaded', function() {
        const firstInput = document.querySelector('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea');
        if (firstInput) {
            firstInput.focus();
        }
    });
</script>
@stack('scripts')
</body>
</html>
