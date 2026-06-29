<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @include('components.loading-overlay')
    @include('components.toast')

    <aside class="gpa-sidebar" id="gpa-sidebar">
        <div class="gpa-sidebar-brand text-center">
            <img src="{{ asset('assets/img/logo-placeholder.svg') }}" alt="Logo Perusahaan" class="mb-2">
            <div class="small text-white-50">Absensi Security</div>
        </div>
        <nav class="gpa-sidebar-nav">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Master Pegawai
                </a>
                <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Data Absensi
                </a>
            @else
                <a href="{{ route('security.attendance.index') }}" class="nav-link {{ request()->routeIs('security.attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-camera-video"></i> Absensi
                </a>
                <a href="{{ route('security.history.index') }}" class="nav-link {{ request()->routeIs('security.history.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Absensi
                </a>
            @endif
        </nav>
    </aside>

    <div class="gpa-main">
        <header class="gpa-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" onclick="GpaApp.toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    <span class="badge bg-secondary ms-1">{{ auth()->user()->role->label() }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="gpa-content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
