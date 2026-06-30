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
    <div id="gpa-secure-banner" class="gpa-secure-banner">
        <i class="bi bi-shield-exclamation me-1"></i>
        Kamera dan GPS membutuhkan HTTPS atau localhost. Gunakan koneksi aman agar absensi berfungsi.
    </div>

    @include('components.loading-overlay')
    @include('components.toast')

    <aside class="gpa-sidebar" id="gpa-sidebar">
        <div class="gpa-sidebar-brand text-center">
            <img src="{{ asset('assets/img/logo-placeholder.svg') }}" alt="Logo" class="mb-2">
            <div class="small text-white-50">Absensi Security GPA</div>
        </div>
        <nav class="gpa-sidebar-nav">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Master Pegawai</a>
                <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}"><i class="bi bi-calendar-check"></i> Data Absensi</a>
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Log Aktivitas</a>
            @else
                <a href="{{ route('employee.attendance.index') }}" class="nav-link {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}"><i class="bi bi-camera-video"></i> Absensi</a>
                <a href="{{ route('employee.history.index') }}" class="nav-link {{ request()->routeIs('employee.history.*') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Riwayat Saya</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> Profil Saya</a>
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="bi bi-bell"></i> Notifikasi</a>
        </nav>
    </aside>

    <div class="gpa-main">
        <header class="gpa-navbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" onclick="GpaApp.toggleSidebar()"><i class="bi bi-list"></i></button>
                <div>
                    <h1 class="h5 mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')<div class="gpa-page-subtitle">@yield('page-subtitle')</div>@endif
                    <div class="gpa-live-clock small text-muted" id="gpa-live-clock"></div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="gpa-theme-toggle" onclick="GpaApp.toggleTheme()" title="Dark mode"><i class="bi bi-moon-fill" id="gpa-theme-icon"></i></button>
                <div class="dropdown">
                    <button class="gpa-notif-bell dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="gpa-notif-badge" id="gpa-notif-count" style="display:none">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end gpa-notif-dropdown p-0">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <span class="fw-semibold small">Notifikasi</span>
                            <button type="button" class="btn btn-link btn-sm p-0" onclick="GpaApp.markAllNotificationsRead()">Tandai dibaca</button>
                        </div>
                        <div id="gpa-notif-list"></div>
                        <div class="text-center py-2 border-top">
                            <a href="{{ route('notifications.index') }}" class="small">Lihat semua</a>
                        </div>
                    </div>
                </div>
                <span class="text-muted small d-none d-md-inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </header>

        <main class="gpa-content">
            @hasSection('breadcrumb')@yield('breadcrumb')@endif
            @yield('content')
        </main>
        @include('components.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
