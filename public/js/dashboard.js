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
                      <div class="container-actions">
                <button class="action-btn start-stop-btn"
                        data-container-id="${site.ID || site.Names}"
                        data-current-state="${site.State}"
                        onclick="toggleContainer(this)">
                    ${site.State === 'running' ? 'Durdur' : 'Başlat'}
                </button>

                <button class="action-btn manage-btn"
                        data-container-id="${site.ID || site.Names}"
                        onclick="showManagePopup(this)">
                    Yönet
                </button>
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
        <div class="site-card ${docker_container_item.State === 'running' ? '' : 'inactive'}">
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
                        onclick="toggleContainer(this)"
                >
                     ${docker_container_item.State === 'running' ? '||' : '▶'}
                </button>


                 <button class="action-btn" onclick="openTerminal('${docker_container_item.ID}')">
                    🖥️
                </button>

                <button class="action-btn" onclick="showLogs('${docker_container_item.ID}')">
                    📄
                </button>

                <button class="action-btn" onclick="showStats('${docker_container_item.ID}')">
                    📊
                </button>

                <button class="action-btn" onclick="restartContainer('${docker_container_item.ID}')">
                    🔄
                </button>

                <button class="action-btn" onclick="removeContainer('${docker_container_item.ID}')">
                    🗑
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
                    <iframe src="/docker/terminal/${containerId}" frameborder="0" width="100%" height="100%"></iframe>
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
