@extends('layouts.app')

@section('content')

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

@endsection


@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
