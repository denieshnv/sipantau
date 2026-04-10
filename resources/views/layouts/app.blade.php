<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPANTAU') - Sistem Monitoring SPJ</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #2E7D32;
            --primary-light: #43A047;
            --primary-dark: #1B5E20;
            --accent: #66BB6A;
            --accent-light: #A5D6A7;
            --bg-dark: #1A2E1A;
            --bg-card: #f8faf8;
            --text-muted: #64748b;
            --success: #2E7D32;
            --danger: #dc2626;
            --border: #e0e8e0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            color: #1e293b;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--bg-dark) 0%, var(--primary-dark) 100%);
            color: #fff;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .brand-icon {
            width: 52px;
            height: 52px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 0;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-brand h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar-brand small {
            font-size: 0.65rem;
            opacity: 0.6;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.35);
            padding: 0.75rem 1.25rem 0.35rem;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            font-weight: 400;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-nav .nav-link.active {
            color: var(--accent-light);
            background: rgba(102, 187, 106, 0.12);
            border-left-color: var(--accent);
            font-weight: 500;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        .sidebar-user {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-user .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .sidebar-user .user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user .user-role {
            font-size: 0.65rem;
            opacity: 0.5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 0.85rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .topbar .page-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--primary);
        }

        .topbar .breadcrumb {
            font-size: 0.78rem;
            margin: 0;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* ===== CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== TABLE ===== */
        .table-container {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-container .table {
            margin: 0;
        }

        .table-container .table thead th {
            background: var(--bg-card);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
            padding: 0.85rem 1rem;
        }

        .table-container .table td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }

        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--primary-dark);
            font-weight: 600;
        }

        .btn-accent:hover {
            background: var(--accent-light);
            border-color: var(--accent-light);
            color: var(--primary-dark);
        }

        /* ===== BADGE ===== */
        .badge-role {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3em 0.7em;
            border-radius: 6px;
        }

        .badge-superadmin {
            background: rgba(46, 125, 50, 0.12);
            color: #2E7D32;
        }

        .badge-admin {
            background: rgba(5, 150, 105, 0.12);
            color: var(--success);
        }

        .year-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
        }

        .topbar .year-badge {
            font-size: 0.78rem;
            padding: 0.3rem 0.75rem;
        }

        /* ===== PROGRESS ===== */
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e2e8f0;
        }

        .progress-bar {
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        /* ===== ALERTS ===== */
        .alert {
            border-radius: 10px;
            font-size: 0.875rem;
            border: none;
        }

        /* ===== FORM ===== */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.35rem;
        }

        /* ===== MOBILE ===== */
        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
                background: none;
                border: none;
                font-size: 1.25rem;
                color: var(--primary);
                cursor: pointer;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo.png') }}" alt="SIPANTAU Logo">
            </div>
            <div>
                <h5>SIPANTAU</h5>
                <small>Monitoring SPJ Kecamatan</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->canManageData())
                {{-- Superadmin & Kasubag PK --}}
                <div class="nav-section-title">Dashboard</div>
                <a href="{{ route('superadmin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard Monitoring
                </a>

                <div class="nav-section-title">Data Master</div>
                <a href="{{ route('superadmin.programs.index') }}"
                    class="nav-link {{ request()->routeIs('superadmin.programs.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> Program
                </a>
                <a href="{{ route('superadmin.kegiatans.index') }}"
                    class="nav-link {{ request()->routeIs('superadmin.kegiatans.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Kegiatan
                </a>
                <a href="{{ route('superadmin.subkegiatans.index') }}"
                    class="nav-link {{ request()->routeIs('superadmin.subkegiatans.*') ? 'active' : '' }}">
                    <i class="bi bi-list-task"></i> Sub Kegiatan
                </a>
                <a href="{{ route('superadmin.import.form') }}"
                    class="nav-link {{ request()->routeIs('superadmin.import.*') ? 'active' : '' }}">
                    <i class="bi bi-cloud-arrow-up-fill"></i> Import Data
                </a>

                <div class="nav-section-title">Dokumen SPJ</div>
                <a href="{{ route('superadmin.dokumen.index') }}"
                    class="nav-link {{ request()->routeIs('superadmin.dokumen.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Semua Dokumen
                </a>
                @if(auth()->user()->canDownloadAll())
                    <a href="{{ route('dokumen.download-all') }}" class="nav-link">
                        <i class="bi bi-file-earmark-zip-fill"></i> Download Semua (ZIP)
                    </a>
                @endif

                @if(auth()->user()->canManageUsers())
                    <div class="nav-section-title">Manajemen</div>
                    <a href="{{ route('superadmin.users.index') }}"
                        class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Kelola User
                    </a>
                @endif

                @if(auth()->user()->canManageYears())
                    <a href="{{ route('superadmin.tahun.index') }}"
                        class="nav-link {{ request()->routeIs('superadmin.tahun.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3"></i> Kelola Tahun
                    </a>
                @endif
            @endif

            @if(auth()->user()->isPptk())
                {{-- PPTK --}}
                <div class="nav-section-title">Menu PPTK</div>
                <a href="{{ route('pptk.dokumen.index') }}"
                    class="nav-link {{ request()->routeIs('pptk.dokumen.index') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Dokumen SPJ Saya
                </a>
                <a href="{{ route('pptk.dokumen.create') }}"
                    class="nav-link {{ request()->routeIs('pptk.dokumen.create') ? 'active' : '' }}">
                    <i class="bi bi-cloud-arrow-up"></i> Unggah SPJ
                </a>
            @endif

            @if(auth()->user()->isCamat())
                {{-- Camat --}}
                <div class="nav-section-title">Menu Camat</div>
                <a href="{{ route('camat.dashboard') }}"
                    class="nav-link {{ request()->routeIs('camat.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Dashboard Progress
                </a>
                <a href="{{ route('camat.dokumen.index') }}"
                    class="nav-link {{ request()->routeIs('camat.dokumen.index') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Dokumen SPJ
                </a>
                <a href="{{ route('dokumen.download-all') }}" class="nav-link">
                    <i class="bi bi-file-earmark-zip-fill"></i> Download Semua (ZIP)
                </a>
            @endif
        </nav>

        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role_label }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm text-white-50 p-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(session('selected_year'))
                    <span class="year-badge">
                        <i class="bi bi-calendar-event"></i> T.A {{ session('selected_year') }}
                    </span>
                @endif
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>

        <div class="content-wrapper">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>

</html>