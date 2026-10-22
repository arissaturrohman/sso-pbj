<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'SSO PBJ Demak')
    </title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- favicon --}}
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        /* =========================================
           TEMA: Soft Blue Elegant
           (konsisten dengan halaman login)
           -----------------------------------------
           Primary       : #3B6EA5
           Primary Dark  : #2C5282
           Primary Light : #E8F0F9
           Surface       : #F5F8FC
           Border        : #E1E8F0
           Text          : #2D3748
           Muted         : #718096
           Accent        : #6B9BD1
        ========================================= */

        :root {
            --brand-primary: #3B6EA5;
            --brand-primary-dark: #2C5282;
            --brand-primary-light: #E8F0F9;
            --brand-surface: #F5F8FC;
            --brand-border: #E1E8F0;
            --brand-text: #2D3748;
            --brand-muted: #718096;
            --brand-accent: #6B9BD1;
        }

        body {
            background-color: var(--brand-surface);
            color: var(--brand-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background-color: var(--brand-primary) !important;
            box-shadow: 0 2px 12px rgba(59, 110, 165, 0.15) !important;
        }

        .navbar-brand {
            letter-spacing: .5px;
            color: #fff !important;
        }

        .navbar .btn-light {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            transition: all 0.25s ease;
        }

        .navbar .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #ffffff;
            border-right: 1px solid var(--brand-border);
        }

        .sidebar .text-uppercase {
            color: var(--brand-muted) !important;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .sidebar .nav-link {
            color: var(--brand-text);
            border-radius: 10px;
            margin-bottom: 4px;
            padding: 10px 14px;
            font-size: 0.925rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            width: 22px;
            color: var(--brand-muted);
            transition: color 0.2s ease;
        }

        .sidebar .nav-link:hover {
            background-color: var(--brand-primary-light);
            color: var(--brand-primary-dark);
        }

        .sidebar .nav-link:hover i {
            color: var(--brand-primary);
        }

        .sidebar .nav-link.active {
            background-color: var(--brand-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 110, 165, 0.25);
        }

        .sidebar .nav-link.active i {
            color: #fff;
        }

        /* ===== CONTENT ===== */
        .content-wrapper {
            min-height: calc(100vh - 56px);
        }

        /* ===== CARD ===== */
        .card {
            border-radius: 14px;
            border: 1px solid var(--brand-border);
            box-shadow: 0 4px 16px rgba(59, 110, 165, 0.06);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--brand-border);
            font-weight: 600;
            color: var(--brand-primary-dark);
            border-radius: 14px 14px 0 0 !important;
        }

        /* ===== TABLE ===== */
        .table th {
            white-space: nowrap;
            background-color: var(--brand-surface);
            color: var(--brand-primary-dark);
            font-weight: 600;
            border-bottom: 1px solid var(--brand-border);
        }

        .table td {
            vertical-align: middle;
            border-color: var(--brand-border);
        }

        .table-hover tbody tr:hover {
            background-color: var(--brand-primary-light);
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--brand-primary-dark);
            border-color: var(--brand-primary-dark);
            box-shadow: 0 4px 12px rgba(59, 110, 165, 0.3);
        }

        .btn-outline-primary {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        /* ===== FORM ===== */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid var(--brand-border);
            color: var(--brand-text);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.15);
        }

        .form-label {
            color: #4A5568;
            font-weight: 500;
        }

        /* ===== LINK ===== */
        a {
            color: var(--brand-primary);
        }

        a:hover {
            color: var(--brand-primary-dark);
        }

        /* ===== BADGE ===== */
        .badge.bg-primary {
            background-color: var(--brand-primary) !important;
        }

        /* ===== PAGINATION ===== */
        .page-link {
            color: var(--brand-primary);
            border-color: var(--brand-border);
        }

        .page-link:hover {
            color: var(--brand-primary-dark);
            background-color: var(--brand-primary-light);
        }

        .page-item.active .page-link {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        /* ===== ALERT ===== */
        .alert-info {
            background-color: var(--brand-primary-light);
            border-color: var(--brand-border);
            color: var(--brand-primary-dark);
        }

        /* ===== NAVBAR RESPONSIVE FIX ===== */
        .navbar {
            min-height: 56px;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .navbar-brand {
            font-size: 1.1rem;
            white-space: nowrap;
        }

        .navbar .btn-logout {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .navbar .btn-logout:hover,
        .navbar .btn-logout:focus {
            background-color: rgba(255, 255, 255, 0.28);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Avatar untuk mobile */
        .user-avatar-nav {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* ===== SIDEBAR RESPONSIVE ===== */
        @media (max-width: 767.98px) {

            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid var(--brand-border);
            }

            .content-wrapper {
                min-height: auto;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .navbar .btn-logout {
                padding: 0.4rem 0.6rem;
            }
        }

        /* Popup logout */
        .logout-swal-popup {
            width: 420px !important;
            padding: 32px 28px 26px !important;
            border-radius: 22px !important;
            background: #ffffff !important;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.16) !important;
            font-family: inherit;
        }

        /* Judul */
        .logout-swal-title {
            color: #172b4d !important;
            font-size: 23px !important;
            font-weight: 750 !important;
            letter-spacing: -0.5px;
            margin-top: 8px !important;
        }

        /* Isi popup */
        .logout-swal-html {
            margin: 10px 0 24px !important;
        }

        .logout-swal-content p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 18px;
        }

        /* Catatan keamanan */
        .logout-swal-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 14px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #475569;
            font-size: 12px;
        }

        .logout-swal-note i {
            color: #0d6efd;
            font-size: 16px;
        }

        /* Area tombol */
        .logout-swal-actions {
            display: flex !important;
            gap: 10px !important;
            width: 100%;
            margin: 0 !important;
        }

        .logout-swal-confirm,
        .logout-swal-cancel {
            flex: 1;
            min-height: 44px;
            padding: 11px 16px !important;
            border: none !important;
            border-radius: 11px !important;
            font-size: 13px !important;
            font-weight: 650 !important;
            transition: all 0.2s ease !important;
            cursor: pointer;
        }

        .logout-swal-confirm {
            background: #dc3545 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.18);
        }

        .logout-swal-confirm:hover {
            background: #bb2d3b !important;
            transform: translateY(-1px);
        }

        .logout-swal-cancel {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        .logout-swal-cancel:hover {
            background: #e2e8f0 !important;
        }

        /* Tombol tutup */
        .logout-swal-close {
            color: #94a3b8 !important;
        }

        .logout-swal-close:hover {
            color: #334155 !important;
        }

        /* Tampilan HP */
        @media (max-width: 480px) {
            .logout-swal-popup {
                width: calc(100% - 24px) !important;
                padding: 28px 20px 22px !important;
                border-radius: 18px !important;
            }

            .logout-swal-title {
                font-size: 21px !important;
            }

            .logout-swal-actions {
                flex-direction: column-reverse !important;
            }

            .logout-swal-confirm,
            .logout-swal-cancel {
                width: 100%;
            }
        }

        /* Pesan validasi saat klik di luar modal */
        .logout-swal-popup .swal2-validation-message {
            margin: 18px 0 8px !important;
            padding: 12px 16px !important;
            border-radius: 10px !important;
            background: #fff7ed !important;
            border: 1px solid #fed7aa !important;
            color: #9a3412 !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
            text-align: center !important;
            box-sizing: border-box;
        }

        /* Beri jarak tambahan antara pesan dan tombol */
        .logout-swal-popup .logout-swal-actions {
            margin-top: 12px !important;
        }

        /* Tampilan HP */
        @media (max-width: 480px) {
            .logout-swal-popup .swal2-validation-message {
                margin: 12px 0 8px !important;
                padding: 12px 16px !important;
                border-radius: 8px !important;
            }
        }

        /* Tampilan HP */
        @media (max-width: 480px) {
            .logout-swal-popup .logout-swal-actions {
                margin-top: 8px !important;
            }
        }

        /* ===== FOOTER CONTENT ===== */
        .main-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            padding: 16px 0;
            margin-top: 24px;
            border-top: 1px solid var(--brand-border);
            color: var(--brand-muted);
            font-size: 12px;
        }

        .main-footer strong {
            color: var(--brand-primary-dark);
            font-weight: 600;
        }

        .main-footer a {
            color: var(--brand-primary);
            text-decoration: none;
        }

        .main-footer a:hover {
            color: var(--brand-primary-dark);
        }

        /* Footer sidebar tetap minimalis */
        .sidebar-footer {
            margin: 16px 15px 10px;
            padding-top: 12px;
            border-top: 1px solid var(--brand-border);
        }

        .sidebar-footer-title {
            color: var(--brand-text);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .sidebar-footer small {
            color: var(--brand-muted);
            font-size: 11px;
        }

        @media (max-width: 767.98px) {
            .main-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }


        .sidebar-session {
            margin: 16px 15px 12px;
            padding: 14px 12px;
            border: 1px solid var(--brand-border);
            border-radius: 12px;
            background: var(--brand-surface);
        }

        .sidebar-session-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-session-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            font-size: 20px;
        }

        .sidebar-session-info {
            min-width: 0;
        }

        .sidebar-session-title {
            color: var(--brand-text);
            font-size: 12px;
            font-weight: 700;
        }

        .sidebar-session-status {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            color: var(--brand-muted);
            font-size: 11px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #16a34a;
        }

        .sidebar-session-divider {
            margin: 12px 0;
            border-top: 1px solid var(--brand-border);
        }

        .sidebar-session-login {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--brand-muted);
        }

        .sidebar-session-login>i {
            margin-top: 2px;
            font-size: 15px;
        }

        .sidebar-session-label {
            margin-bottom: 4px;
            font-size: 10px;
        }

        .sidebar-session-time {
            color: var(--brand-text);
            font-size: 11px;
            font-weight: 600;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }
    </style>

    @stack('styles')

</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand navbar-dark shadow-sm">

        <div class="container-fluid">

            {{-- Brand --}}
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="navbar-brand fw-bold mb-0">
                <i class="bi bi-shield-lock-fill me-1"></i>
                <span class="d-none d-sm-inline">SSO PBJ DEMAK</span>
                <span class="d-inline d-sm-none">SSO</span>
            </a>

            @auth

                <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">

                    {{-- Info user: sembunyikan di mobile sangat kecil --}}
                    <div class="text-white text-end d-none d-md-block">

                        <div class="fw-semibold lh-sm" style="font-size: 0.9rem;">
                            {{ auth()->user()->name }}
                        </div>

                        <small class="opacity-75" style="font-size: 0.75rem;">

                            @forelse(auth()->user()->roles as $role)
                                {{ $role->name }}
                            @empty
                                User
                            @endforelse

                        </small>

                    </div>

                    {{-- Avatar inisial (muncul di mobile saja) --}}
                    <div class="user-avatar-nav d-md-none" title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    {{-- Tombol Logout --}}
                    <form action="{{ route('logout') }}" method="POST" class="mb-0" id="logout-form">
                        @csrf

                        <button type="button" class="btn btn-logout btn-sm d-flex align-items-center gap-1"
                            id="btn-logout">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-sm-inline">Logout</span>
                        </button>
                    </form>

                </div>

            @endauth

        </div>

    </nav>


    @auth

        <div class="container-fluid">

            <div class="row">

                {{-- Sidebar --}}
                <aside class="col-md-3 col-lg-2 sidebar p-3">

                    <div class="mb-3">

                        <small class="text-uppercase text-muted fw-bold">
                            Menu Utama
                        </small>

                    </div>

                    <nav class="nav flex-column">

                        <a href="{{ route('sso.apps') }}"
                            class="nav-link
                                {{ request()->routeIs('sso.apps') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            Aplikasi Saya
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="nav-link
                                {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>

                    </nav>

                    @can('applications.view')
                        <div class="mt-4 mb-3">

                            <small class="text-uppercase text-muted fw-bold">
                                Administrasi
                            </small>

                        </div>
                    @endcan

                    <nav class="nav flex-column">

                        @can('users.view')
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link
                {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i>
                                Manajemen User
                            </a>
                        @endcan


                        @can('roles.view')
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link
                                    {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">

                                <i class="bi bi-shield-lock"></i>
                                Role & Permission

                            </a>
                        @endcan


                        @can('applications.view')
                            <a href="{{ route('admin.applications.index') }}"
                                class="nav-link
                                    {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">

                                <i class="bi bi-grid"></i>
                                Aplikasi SSO

                            </a>
                        @endcan

                    </nav>

                    <hr>

                    <div class="sidebar-session">
                        <div class="sidebar-session-header">
                            <div class="sidebar-session-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <div class="sidebar-session-info">
                                <div class="sidebar-session-title">
                                    Sesi Pengguna
                                </div>

                                <div class="sidebar-session-status">
                                    <span class="status-dot"></span>
                                    Sesi aktif
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-session-divider"></div>

                        <div class="sidebar-session-login">
                            <i class="bi bi-clock-history"></i>

                            <div>
                                <div class="sidebar-session-label">
                                    Login terakhir
                                </div>

                                <div class="sidebar-session-time">
                                    @php
                                        $lastLogin = auth()->user()?->last_login_at;
                                    @endphp

                                    {{ $lastLogin ? $lastLogin->timezone(config('app.timezone'))->format('d/m/Y H:i') . ' WIB' : 'Belum tersedia' }}
                                </div>
                            </div>
                        </div>
                    </div>

                </aside>


                {{-- Content --}}
                <main class="col-md-9 col-lg-10 content-wrapper">

                    <div class="p-3 p-md-4 d-flex flex-column h-100">

                        <div class="flex-grow-1">
                            @yield('content')
                        </div>

                        <footer class="main-footer">
                            <div>
                                &copy; {{ date('Y') }}
                                <strong>Tim IT PBJ Demak</strong>
                            </div>

                            <div>
                                SSO PBJ Demak
                                <span class="mx-1">·</span>
                                Build v1.0.1
                            </div>
                        </footer>

                    </div>

                </main>

            </div>

        </div>
    @else
        @yield('content')

    @endauth


    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnLogout = document.getElementById('btn-logout');
            const logoutForm = document.getElementById('logout-form');

            if (btnLogout && logoutForm) {
                btnLogout.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Keluar dari Aplikasi?',
                        html: `
                            <div class="logout-swal-content">
                                <p>Apakah Anda yakin ingin mengakhiri sesi Anda saat ini?</p>
                                <div class="logout-swal-note">
                                    <i class="bi bi-shield-lock-fill"></i>
                                    <span>Sesi Anda akan diakhiri dengan aman.</span>
                                </div>
                            </div>
                        `,
                        icon: undefined,
                        showCancelButton: true,
                        showCloseButton: false,
                        confirmButtonText: '<i class="bi bi-box-arrow-right"></i> Ya, Logout',
                        cancelButtonText: '<i class="bi bi-arrow-left"></i> Kembali',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'logout-swal-popup',
                            title: 'logout-swal-title',
                            htmlContainer: 'logout-swal-html',
                            actions: 'logout-swal-actions',
                            confirmButton: 'logout-swal-confirm',
                            cancelButton: 'logout-swal-cancel',
                            closeButton: 'logout-swal-close'
                        },
                        showCloseButton: false,
                        reverseButtons: true,
                        focusCancel: true,
                        allowOutsideClick: () => {
                            Swal.showValidationMessage(
                                'Silakan gunakan tombol "Kembali" untuk membatalkan logout.'
                            );
                            return false;
                        },
                        allowEscapeKey: false,
                        heightAuto: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('logout-form').submit();
                        }
                    });

                });
            }
        });
    </script>

</body>

</html>
