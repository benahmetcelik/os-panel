<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
</head>
<link rel="stylesheet" href="{{ asset('css/main.css?v='.rand(0,9999999)) }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<body>


@stack('styles')


<!-- Mobile Menu Button -->
<button class="mobile-menu-btn" onclick="toggleMobileSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

<div class="dashboard-container">
    <!-- Sol Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-server"></i> Server Dashboard</h1>
            <div class="subtitle">Sunucu yönetim paneli</div>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('sites.index') }}" class="menu-item {{ request()->routeIs('sites.*') ? 'active' : '' }}">
                <i class="fas fa-globe"></i>
                Site Yönetimi
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-cogs"></i>
                Sunucu Ayarları
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-server"></i>
                Nginx Yönetimi
            </a>

            <div class="menu-divider"></div>

            <a href="#" class="menu-item">
                <i class="fas fa-shield-alt"></i>
                DNS ve SSL Ayarları
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-puzzle-piece"></i>
                Eklentiler
            </a>

            <div class="menu-divider"></div>

            <a href="#" class="menu-item">
                <i class="fa-brands fa-docker"></i>
                Docker Containerları
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-database"></i>
                Veritabanı Yönetimi
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-users"></i>
                Kullanıcı Yönetimi
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-chart-line"></i>
                Sistem İstatistikleri
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-file-alt"></i>
                Log Yönetimi
            </a>
            <a href="#" class="menu-item">
                <i class="fas fa-backup"></i>
                Yedekleme
            </a>
        </div>
    </div>

    <!-- Ana İçerik Alanı -->
    <div class="main-content">
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div>
                        <h1><i class="fas fa-server"></i> Server Dashboard</h1>
                        <div class="subtitle">Sunucu durumu ve sistem metrikleri</div>
                    </div>


                </div>

                <div class="header-right">
                    <button class="btn btn-success" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Yenile
                    </button>
                </div>
            </div>
        </div>


    @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

    </div>

</div>
</body>
@stack('scripts')

<script src="{{ asset('js/main.js?v='.rand(0,99999)) }}"></script>

</html>
