<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
</head>
<style>
    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }

    /* Sol Sidebar */
    .sidebar {
        width: 280px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        padding: 20px 0;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 0 20px 30px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .sidebar-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-header .subtitle {
        color: #718096;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .sidebar-menu {
        padding: 0 10px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin: 5px 0;
        border-radius: 10px;
        text-decoration: none;
        color: #4a5568;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .menu-item:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateX(5px);
    }

    .menu-item i {
        width: 20px;
        margin-right: 12px;
        text-align: center;
    }

    .menu-divider {
        height: 1px;
        background: rgba(0, 0, 0, 0.1);
        margin: 15px 20px;
    }

    /* Ana İçerik Alanı */
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 20px;
    }

    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .header-left .subtitle {
        color: #718096;
        font-size: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.mobile-open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .mobile-menu-btn {
            display: block;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    }

    .mobile-menu-btn {
        display: none;
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    @media (max-width: 768px) {
        .sidebar-overlay.show {
            display: block;
        }
        .mobile-menu-btn {
            display: block;
        }
    }
</style>
<link rel="stylesheet" href="{{ asset('css/dashboard.css?v='.rand(0,9999).'.'.rand(0,99999)) }}">
<body>


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
            <a href="#" class="menu-item">
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
                    <button class="refresh-btn" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Yenile
                    </button>
                </div>
            </div>
        </div>


        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">CPU Kullanımı</div>
                    <div class="stat-icon cpu-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                </div>
                <div class="stat-value" id="cpu-value">
                    <span class="loading"><span class="spinner"></span> Yükleniyor...</span>
                </div>
                <div class="stat-label">İşlemci yükü</div>
                <div class="progress-bar">
                    <div class="progress-fill cpu-progress" id="cpu-progress" style="width: 0%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">RAM Kullanımı</div>
                    <div class="stat-icon ram-icon">
                        <i class="fas fa-memory"></i>
                    </div>
                </div>
                <div class="stat-value" id="ram-value">
                    <span class="loading"><span class="spinner"></span> Yükleniyor...</span>
                </div>
                <div class="stat-label">Bellek kullanımı</div>
                <div class="progress-bar">
                    <div class="progress-fill ram-progress" id="ram-progress" style="width: 0%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Disk Kullanımı</div>
                    <div class="stat-icon disk-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                </div>
                <div class="stat-value" id="disk-value">
                    <span class="loading"><span class="spinner"></span> Yükleniyor...</span>
                </div>
                <div class="stat-label">Depolama alanı</div>
                <div class="progress-bar">
                    <div class="progress-fill disk-progress" id="disk-progress" style="width: 0%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Ağ Durumu</div>
                    <div class="stat-icon network-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>
                <div class="stat-value" id="network-value">
                    <span class="loading"><span class="spinner"></span> Yükleniyor...</span>
                </div>
                <div class="stat-label">Bağlantı durumu</div>
            </div>
        </div>

        <div class="sites-section">
            <h2 class="section-title">
                <i class="fas fa-globe"></i>
                Nginx Siteleri
            </h2>
            <div class="sites-grid" id="sites-container">
                <div class="loading">
                    <span class="spinner"></span>
                    Siteler yükleniyor...
                </div>
            </div>
        </div>

        <div class="docker-section">
            <h2 class="section-title">
                <i class="fa-brands fa-docker"></i>
                Docker Containers
            </h2>
            <div class="sites-grid" id="docker-container">
                <div class="loading">
                    <span class="spinner"></span>
                    Containers yükleniyor...
                </div>
            </div>
        </div>

        <div class="last-updated" id="last-updated">
            Son güncelleme: --
        </div>
    </div>

</div>
</body>
<script src="{{ asset('js/dashboard.js?v='.rand(0,1000).'.'.rand(0,99999)) }}"></script>

<script>
    // Navigation Menu Toggle
    function toggleNav() {
        const dropdown = document.getElementById('navDropdown');
        const toggle = document.querySelector('.nav-toggle');

        dropdown.classList.toggle('show');
        toggle.classList.toggle('active');

        // Close dropdown when clicking outside
        if (dropdown.classList.contains('show')) {
            document.addEventListener('click', closeNavOnOutsideClick);
        } else {
            document.removeEventListener('click', closeNavOnOutsideClick);
        }
    }

    function closeNavOnOutsideClick(event) {
        const navMenu = document.querySelector('.nav-menu');
        if (!navMenu.contains(event.target)) {
            document.getElementById('navDropdown').classList.remove('show');
            document.querySelector('.nav-toggle').classList.remove('active');
            document.removeEventListener('click', closeNavOnOutsideClick);
        }
    }

    // Close dropdown on ESC key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.getElementById('navDropdown').classList.remove('show');
            document.querySelector('.nav-toggle').classList.remove('active');
        }
    });


    // Mobile Sidebar Toggle
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('show');
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    }

    // Close sidebar on ESC key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

</script>

</html>
