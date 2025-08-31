<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .subtitle {
            color: #7f8c8d;
            margin-top: 5px;
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .stat-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
        }

        .cpu-icon { background: linear-gradient(45deg, #ff6b6b, #ee5a24); }
        .ram-icon { background: linear-gradient(45deg, #4834d4, #686de0); }
        .disk-icon { background: linear-gradient(45deg, #00d2d3, #01a3a4); }
        .network-icon { background: linear-gradient(45deg, #ff9ff3, #f368e0); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 15px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .cpu-progress { background: linear-gradient(90deg, #ff6b6b, #ee5a24); }
        .ram-progress { background: linear-gradient(90deg, #4834d4, #686de0); }
        .disk-progress { background: linear-gradient(90deg, #00d2d3, #01a3a4); }

        .sites-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .docker-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }

        .site-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            border-left: 4px solid #27ae60;
            transition: all 0.3s ease;
        }

        .site-card:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .site-card.inactive {
            border-left-color: #e74c3c;
            opacity: 0.7;
        }

        .site-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .site-url {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .site-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-active { background: #27ae60; }
        .status-inactive { background: #e74c3c; }

        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #7f8c8d;
            font-style: italic;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #ecf0f1;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .last-updated {
            text-align: center;
            color: #7f8c8d;
            margin-top: 30px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container { padding: 15px; }
            .header { padding: 20px; }
            .header h1 { font-size: 1.8rem; }
            .stat-value { font-size: 2rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .sites-grid { grid-template-columns: 1fr; }
        }

        .refresh-btn {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .refresh-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .container-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .start-stop-btn {
            background-color: #28a745;
            color: white;
        }

        .start-stop-btn:hover {
            background-color: #218838;
        }

        .start-stop-btn[data-current-state="running"] {
            background-color: #dc3545;
        }

        .start-stop-btn[data-current-state="running"]:hover {
            background-color: #c82333;
        }

        .manage-btn {
            background-color: #007bff;
            color: white;
        }

        .manage-btn:hover {
            background-color: #0056b3;
        }

        /* Popup stilleri */
        .manage-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .popup-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .popup-option {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            text-align: left;
            transition: background-color 0.2s ease;
        }

        .popup-option:hover {
            background-color: #e9ecef;
        }

        .popup-close {
            display: block;
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .popup-close:hover {
            background-color: #5a6268;
        }

        .container-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    justify-content: flex-end;
}

.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.start-stop-btn {
    background-color: #28a745;
    color: white;
}

.start-stop-btn:hover {
    background-color: #218838;
}

.start-stop-btn[data-current-state="running"] {
    background-color: #dc3545;
}

.start-stop-btn[data-current-state="running"]:hover {
    background-color: #c82333;
}

.manage-btn {
    background-color: #007bff;
    color: white;
}

.manage-btn:hover {
    background-color: #0056b3;
}

/* Popup stilleri */
.manage-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    background: white;
    border-radius: 8px;
    padding: 20px;
    width: 300px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.popup-header {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    text-align: center;
}

.popup-option {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 8px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    text-align: left;
    transition: background-color 0.2s ease;
}

.popup-option:hover {
    background-color: #e9ecef;
}

.popup-close {
    display: block;
    width: 100%;
    padding: 8px;
    margin-top: 10px;
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.popup-close:hover {
    background-color: #5a6268;
}

/* Log Modal Styles */
.log-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.log-modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 1000px;
    max-height: 90vh;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.log-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.log-modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.log-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.log-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.log-modal-body {
    padding: 0;
    height: 600px;
}

.log-modal-body iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 0 0 15px 15px;
}

@media (max-width: 768px) {
    .log-modal-content {
        width: 95%;
        max-height: 95vh;
    }

    .log-modal-body {
        height: 500px;
    }

    .log-modal-header h3 {
        font-size: 1rem;
    }
}

/* Terminal Modal Styles */
.terminal-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.terminal-modal-content {
    background: #1a1a1a;
    border-radius: 15px;
    width: 95%;
    max-width: 1200px;
    max-height: 95vh;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    overflow: hidden;
    animation: terminalModalSlideIn 0.3s ease;
}

@keyframes terminalModalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.terminal-modal-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #444;
}

.terminal-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

.terminal-modal-close {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.terminal-modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.terminal-modal-body {
    padding: 0;
    height: 700px;
    background: #000;
}

.terminal-modal-body iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 0 0 15px 15px;
    background: #000;
}

@media (max-width: 768px) {
    .terminal-modal-content {
        width: 98%;
        max-height: 98vh;
    }

    .terminal-modal-body {
        height: 600px;
    }

    .terminal-modal-header h3 {
        font-size: 0.9rem;
    }
}

/* Stats Modal Styles */
.stats-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.stats-modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 1100px;
    max-height: 90vh;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: statsModalSlideIn 0.3s ease;
}

@keyframes statsModalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.stats-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stats-modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.stats-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.stats-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.stats-modal-body {
    padding: 0;
    height: 650px;
}

.stats-modal-body iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 0 0 15px 15px;
}

@media (max-width: 768px) {
    .stats-modal-content {
        width: 95%;
        max-height: 95vh;
    }

    .stats-modal-body {
        height: 550px;
    }

    .stats-modal-header h3 {
        font-size: 1rem;
    }
}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><i class="fas fa-server"></i> Server Dashboard</h1>
                <div class="subtitle">Sunucu durumu ve sistem metrikleri</div>
            </div>
            <button class="refresh-btn" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Yenile
            </button>
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
            <i class="fas fa-globe"></i>
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

<script>
    let refreshInterval;

    // API endpoint'leri (Laravel route'larınızla eşleştirin)
    const API_ENDPOINTS = {
        stats: '/api/server/stats',
        sites: '/api/server/nginx-sites',
        containers: '/api/server/docker-containers'
    };

    // Sayfa yüklendiğinde veriyi getir
    document.addEventListener('DOMContentLoaded', function() {
        loadServerData();
        // Her 30 saniyede bir otomatik yenile
        refreshInterval = setInterval(loadServerData, 30000);
    });

    async function loadServerData() {
        try {
            await Promise.all([
                loadSystemStats(),
                loadNginxSites(),
                loadDockerContainers()
            ]);
            updateLastUpdated();
        } catch (error) {
            console.error('Veri yükleme hatası:', error);
        }
    }

    async function loadSystemStats() {
        try {
            const response = await fetch(API_ENDPOINTS.stats);
            const data = await response.json();

            if (data.success) {
                updateSystemStats(data.data);
            } else {
                throw new Error(data.message || 'Sistem bilgileri alınamadı');
            }
        } catch (error) {
            console.error('Sistem istatistikleri hatası:', error);
            showError('cpu-value', 'Hata');
            showError('ram-value', 'Hata');
            showError('disk-value', 'Hata');
            showError('network-value', 'Hata');
        }
    }

    function updateSystemStats(stats) {
        // CPU kullanımı
        document.getElementById('cpu-value').textContent = `${stats.cpu.usage}%`;
        document.getElementById('cpu-progress').style.width = `${stats.cpu.usage}%`;

        // RAM kullanımı
        const ramUsage = ((stats.memory.used / stats.memory.total) * 100).toFixed(1);
        document.getElementById('ram-value').innerHTML = `
                ${formatBytes(stats.memory.used)}<br>
                <small style="font-size: 0.6em; color: #7f8c8d;">/ ${formatBytes(stats.memory.total)}</small>
            `;
        document.getElementById('ram-progress').style.width = `${ramUsage}%`;

        // Disk kullanımı
        const diskUsage = ((stats.disk.used / stats.disk.total) * 100).toFixed(1);
        document.getElementById('disk-value').innerHTML = `
                ${formatBytes(stats.disk.used)}<br>
                <small style="font-size: 0.6em; color: #7f8c8d;">/ ${formatBytes(stats.disk.total)}</small>
            `;
        document.getElementById('disk-progress').style.width = `${diskUsage}%`;

        // Ağ durumu
        document.getElementById('network-value').innerHTML = `
                <i class="fas fa-arrow-up" style="color: #27ae60;"></i> ${formatBytes(stats.network.upload_speed)}/s<br>
                <i class="fas fa-arrow-down" style="color: #3498db;"></i> ${formatBytes(stats.network.download_speed)}/s
            `;
    }

    async function loadNginxSites() {
        try {
            const response = await fetch(API_ENDPOINTS.sites);
            const data = await response.json();

            if (data.success) {
                displayNginxSites(data.data);
            } else {
                throw new Error(data.message || 'Site bilgileri alınamadı');
            }
        } catch (error) {
            console.error('Nginx site hatası:', error);
            document.getElementById('sites-container').innerHTML = `
                    <div style="color: #e74c3c; text-align: center; grid-column: 1 / -1;">
                        <i class="fas fa-exclamation-triangle"></i> Siteler yüklenemedi
                    </div>
                `;
        }
    }

    function displayNginxSites(sites) {
        const container = document.getElementById('sites-container');

        if (sites.length === 0) {
            container.innerHTML = `
                    <div style="text-align: center; color: #7f8c8d; grid-column: 1 / -1;">
                        <i class="fas fa-info-circle"></i> Aktif site bulunamadı
                    </div>
                `;
            return;
        }

        container.innerHTML = sites.map(site => `
                <div class="site-card ${site.status === 'active' ? '' : 'inactive'}">
                    <div class="site-name">${site.name}</div>
                    <div class="site-url">${site.domain}</div>
                    <div class="site-status">
                        <span class="status-dot ${site.status === 'active' ? 'status-active' : 'status-inactive'}"></span>
                        ${site.status === 'active' ? 'Aktif' : 'Pasif'}
                        ${site.ssl ? '<i class="fas fa-lock" style="color: #27ae60; margin-left: 10px;" title="SSL Aktif"></i>' : ''}
                    </div>
                </div>
            `).join('');
    }




    async function loadDockerContainers() {
        try {
            const response = await fetch(API_ENDPOINTS.containers);
            const data = await response.json();

            if (data.success) {
                displayDockerContainers(data.data);
            } else {
                throw new Error(data.message || 'Container bilgileri alınamadı');
            }
        } catch (error) {
            console.error('Docker Container hatası:', error);
            document.getElementById('docker-container').innerHTML = `
                    <div style="color: #e74c3c; text-align: center; grid-column: 1 / -1;">
                        <i class="fas fa-exclamation-triangle"></i> Containers yüklenemedi
                    </div>
                `;
        }
    }

    function displayDockerContainers(docker_container) {
        const container = document.getElementById('docker-container');

        if (docker_container.length === 0) {
            container.innerHTML = `
                    <div style="text-align: center; color: #7f8c8d; grid-column: 1 / -1;">
                        <i class="fas fa-info-circle"></i> Aktif container bulunamadı
                    </div>
                `;
            return;
        }

        container.innerHTML = docker_container.map(docker_container_item => `
        <div class="site-card ${docker_container_item.status === 'active' ? '' : 'inactive'}">
            <div class="site-name">${docker_container_item.Names}</div>
            <div class="site-url">${docker_container_item.Ports}</div>
            <div class="site-status">
                <span class="status-dot ${docker_container_item.State === 'running' ? 'status-active' : 'status-inactive'}"></span>
                ${docker_container_item.State === 'running' ? 'Aktif' : 'Pasif'}
            </div>
            <br> CPU : ${docker_container_item.CPUPerc} - RAM : ${docker_container_item.MemUsage}

            <div class="container-actions">
                <button class="action-btn start-stop-btn"
                        data-container-id="${docker_container_item.ID || docker_container_item.Names}"
                        data-current-state="${docker_container_item.State}"
                        onclick="toggleContainer(this)">
                    ${docker_container_item.State === 'running' ? 'Durdur' : 'Başlat'}
                </button>

                <button class="action-btn manage-btn"
                        data-container-id="${docker_container_item.ID || docker_container_item.Names}"
                        onclick="showManagePopup(this)">
                    Yönet
                </button>
            </div>
        </div>
    `).join('');
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function showError(elementId, message) {
        document.getElementById(elementId).innerHTML = `<span style="color: #e74c3c;">${message}</span>`;
    }

    function updateLastUpdated() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('tr-TR');
        document.getElementById('last-updated').textContent = `Son güncelleme: ${timeString}`;
    }

    async function refreshData() {
        const refreshBtn = document.querySelector('.refresh-btn');
        const icon = refreshBtn.querySelector('i');

        refreshBtn.disabled = true;
        icon.style.animation = 'spin 1s linear infinite';

        try {
            await loadServerData();
        } finally {
            setTimeout(() => {
                refreshBtn.disabled = false;
                icon.style.animation = '';
            }, 1000);
        }
    }

    // Sayfa kapatıldığında interval'ı temizle
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });


    container.innerHTML = docker_container.map(docker_container_item => `
        <div class="site-card ${docker_container_item.status === 'active' ? '' : 'inactive'}">
            <div class="site-name">${docker_container_item.Names}</div>
            <div class="site-url">${docker_container_item.Ports}</div>
            <div class="site-status">
                <span class="status-dot ${docker_container_item.State === 'running' ? 'status-active' : 'status-inactive'}"></span>
                ${docker_container_item.State === 'running' ? 'Aktif' : 'Pasif'}
            </div>
            <br> CPU : ${docker_container_item.CPUPerc} - RAM : ${docker_container_item.MemUsage}

            <div class="container-actions">
                <button class="action-btn start-stop-btn"
                        data-container-id="${docker_container_item.ID || docker_container_item.Names}"
                        data-current-state="${docker_container_item.State}"
                        onclick="toggleContainer(this)">
                    ${docker_container_item.State === 'running' ? 'Durdur' : 'Başlat'}
                </button>

                <button class="action-btn manage-btn"
                        data-container-id="${docker_container_item.ID || docker_container_item.Names}"
                        onclick="showManagePopup(this)">
                    Yönet
                </button>
            </div>
        </div>
    `).join('');



    // JavaScript fonksiyonları
    function toggleContainer(button) {
        const containerId = button.dataset.containerId;
        const currentState = button.dataset.currentState;

        // Buton durumunu güncelle
        button.disabled = true;
        button.textContent = 'İşleniyor...';

        // API çağrısı (örnek)
        const action = currentState === 'running' ? 'stop' : 'start';

        // Burada gerçek API çağrınızı yapın
        fetch(`/api/server/container/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ containerId: containerId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı olursa buton durumunu güncelle
                    const newState = currentState === 'running' ? 'stopped' : 'running';
                    button.dataset.currentState = newState;
                    button.textContent = newState === 'running' ? 'Durdur' : 'Başlat';

                    // Sayfa yenilenmesi için container listesini yeniden yükle
                    location.reload();
                } else {
                    throw new Error(data.message || 'İşlem başarısız');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('İşlem başarısız: ' + error.message);
                button.textContent = currentState === 'running' ? 'Durdur' : 'Başlat';
            })
            .finally(() => {
                button.disabled = false;
            });
    }

    function showManagePopup(button) {
        const containerId = button.dataset.containerId;

        // Popup HTML'i oluştur
        const popupHTML = `
        <div class="manage-popup" id="manage-popup">
            <div class="popup-content">
                <div class="popup-header">Container Yönetimi</div>

                <button class="popup-option" onclick="openTerminal('${containerId}')">
                    🖥️ Terminal Erişimi
                </button>

                <button class="popup-option" onclick="showLogs('${containerId}')">
                    📄 Logları Görüntüle
                </button>

                <button class="popup-option" onclick="showStats('${containerId}')">
                    📊 İstatistikler
                </button>

                <button class="popup-option" onclick="restartContainer('${containerId}')">
                    🔄 Yeniden Başlat
                </button>

                <button class="popup-option" onclick="removeContainer('${containerId}')">
                    🗑️ Container'ı Sil
                </button>

                <button class="popup-close" onclick="closeManagePopup()">
                    Kapat
                </button>
            </div>
        </div>
    `;

        // Popup'ı sayfaya ekle
        document.body.insertAdjacentHTML('beforeend', popupHTML);
    }

    function closeManagePopup() {
        const popup = document.getElementById('manage-popup');
        if (popup) {
            popup.remove();
        }
    }

    function openTerminal(containerId) {
        closeManagePopup();

        // Terminal modal'ını oluştur
        const terminalModalHTML = `
        <div class="terminal-modal" id="terminal-modal">
            <div class="terminal-modal-content">
                <div class="terminal-modal-header">
                    <h3>🖥️ Terminal Erişimi - ${containerId}</h3>
                    <button class="terminal-modal-close" onclick="closeTerminalModal()">✕</button>
                </div>
                <div class="terminal-modal-body">
                    <iframe src="/test-terminal?container=${containerId}" frameborder="0" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
        `;

        // Modal'ı sayfaya ekle
        document.body.insertAdjacentHTML('beforeend', terminalModalHTML);
    }

    function closeTerminalModal() {
        const modal = document.getElementById('terminal-modal');
        if (modal) {
            // iframe'e kapatma mesajı gönder
            const iframe = modal.querySelector('iframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.postMessage('close', '*');
                } catch (e) {
                    // iframe cross-origin olabilir, hata yoksay
                }
            }
            modal.remove();
        }
    }

    function showLogs(containerId) {
        closeManagePopup();

        // Log modal'ını oluştur
        const logModalHTML = `
        <div class="log-modal" id="log-modal">
            <div class="log-modal-content">
                <div class="log-modal-header">
                    <h3>📄 Container Logları - ${containerId}</h3>
                    <button class="log-modal-close" onclick="closeLogModal()">✕</button>
                </div>
                <div class="log-modal-body">
                    <iframe src="/containers/logs/${containerId}" frameborder="0" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
        `;

        // Modal'ı sayfaya ekle
        document.body.insertAdjacentHTML('beforeend', logModalHTML);
    }

    function closeLogModal() {
        const modal = document.getElementById('log-modal');
        if (modal) {
            // iframe'e kapatma mesajı gönder
            const iframe = modal.querySelector('iframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.postMessage('close', '*');
                } catch (e) {
                    // iframe cross-origin olabilir, hata yoksay
                }
            }
            modal.remove();
        }
    }

            // ESC tuşu ile modal kapatma
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLogModal();
            closeTerminalModal();
            closeStatsModal();
            closeManagePopup();
        }
    });

    // Modal dışına tıklayınca kapatma
    document.addEventListener('click', function(event) {
        const logModal = document.getElementById('log-modal');
        if (logModal && event.target === logModal) {
            closeLogModal();
        }

        const terminalModal = document.getElementById('terminal-modal');
        if (terminalModal && event.target === terminalModal) {
            closeTerminalModal();
        }

        const statsModal = document.getElementById('stats-modal');
        if (statsModal && event.target === statsModal) {
            closeStatsModal();
        }
    });

    function showStats(containerId) {
        closeManagePopup();

        // Stats modal'ını oluştur
        const statsModalHTML = `
        <div class="stats-modal" id="stats-modal">
            <div class="stats-modal-content">
                <div class="stats-modal-header">
                    <h3>📊 Container İstatistikleri - ${containerId}</h3>
                    <button class="stats-modal-close" onclick="closeStatsModal()">✕</button>
                </div>
                <div class="stats-modal-body">
                    <iframe src="/docker/stats/${containerId}" frameborder="0" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
        `;

        // Modal'ı sayfaya ekle
        document.body.insertAdjacentHTML('beforeend', statsModalHTML);
    }

    function closeStatsModal() {
        const modal = document.getElementById('stats-modal');
        if (modal) {
            // iframe'e kapatma mesajı gönder
            const iframe = modal.querySelector('iframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.postMessage('close', '*');
                } catch (e) {
                    // iframe cross-origin olabilir, hata yoksay
                }
            }
            modal.remove();
        }
    }

    function restartContainer(containerId) {
        closeManagePopup();

        if (confirm('Container\'ı yeniden başlatmak istediğinizden emin misiniz?')) {
            fetch(`/api/server/container/restart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ containerId: containerId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Container başarıyla yeniden başlatıldı');
                        location.reload();
                    } else {
                        alert('Yeniden başlatma başarısız: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('Yeniden başlatma başarısız: ' + error.message);
                });
        }
    }

    function removeContainer(containerId) {
        closeManagePopup();

        if (confirm('Container\'ı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
            fetch(`/api/server/container/remove`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ containerId: containerId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Container başarıyla silindi');
                        location.reload();
                    } else {
                        alert('Silme işlemi başarısız: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('Silme işlemi başarısız: ' + error.message);
                });
        }
    }

    // Popup dışına tıklandığında kapat
    document.addEventListener('click', function(event) {
        const popup = document.getElementById('manage-popup');
        if (popup && event.target === popup) {
            closeManagePopup();
        }
    });
</script>
</body>
</html>
