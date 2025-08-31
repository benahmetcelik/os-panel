<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Container Stats - {{ $id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #e0e0e0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .stats-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .stats-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }

        .stats-status {
            font-size: 0.9rem;
            color: #888;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .dashboard-container {
            padding: 30px;
            background: transparent;
            min-height: calc(100vh - 80px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card h3 {
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card span, .stat-card small {
            display: block;
            font-weight: 600;
            margin-top: 8px;
            font-size: 1rem;
            color: #e0e0e0;
        }

        .stat-card small {
            font-size: 0.85rem;
            color: #888;
            margin-top: 5px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
            position: relative;
        }

        .fill {
            height: 100%;
            width: 0%;
            border-radius: 10px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .cpu-fill {
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e, #ff6b6b);
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
        }

        .mem-fill {
            background: linear-gradient(90deg, #4ecdc4, #44a08d, #4ecdc4);
            box-shadow: 0 0 20px rgba(78, 205, 196, 0.5);
        }

        .loading {
            text-align: center;
            padding: 60px 20px;
            color: #888;
            font-style: italic;
            font-size: 1.1rem;
        }

        .error {
            text-align: center;
            padding: 60px 20px;
            color: #ff6b6b;
            font-size: 1.1rem;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 15px;
            margin: 20px;
        }

        /* Özel stat card stilleri */
        .stat-card.cpu-card {
            border-color: rgba(255, 107, 107, 0.3);
        }

        .stat-card.memory-card {
            border-color: rgba(78, 205, 196, 0.3);
        }

        .stat-card.network-card {
            border-color: rgba(102, 126, 234, 0.3);
        }

        .stat-card.block-card {
            border-color: rgba(240, 147, 251, 0.3);
        }

        .stat-card.pids-card {
            border-color: rgba(255, 193, 7, 0.3);
        }

        /* Responsive tasarım */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stat-card {
                padding: 20px;
            }

            .dashboard-container {
                padding: 20px;
            }

            .stats-header {
                padding: 15px 20px;
            }

            .stats-title {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                gap: 15px;
            }

            .stat-card {
                padding: 15px;
            }

            .dashboard-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="stats-header">
        <div class="stats-title">📊 {{ $id }}</div>
        <div class="stats-status" id="stats-status">🔄 Yükleniyor...</div>
    </div>

    <div class="dashboard-container">
        <div class="stats-grid">
            <div class="stat-card cpu-card">
                <h3>CPU Kullanımı</h3>
                <div class="progress-bar">
                    <div id="cpu-bar" class="fill cpu-fill"></div>
                </div>
                <span id="cpu">0%</span>
            </div>

            <div class="stat-card memory-card">
                <h3>Bellek Kullanımı</h3>
                <div class="progress-bar">
                    <div id="mem-bar" class="fill mem-fill"></div>
                </div>
                <span id="memUsage">0 / 0</span>
                <small id="memPerc">(0%)</small>
            </div>

            <div class="stat-card network-card">
                <h3>Ağ I/O</h3>
                <span id="netIO">0 / 0</span>
            </div>

            <div class="stat-card block-card">
                <h3>Disk I/O</h3>
                <span id="blockIO">0 / 0</span>
            </div>

            <div class="stat-card pids-card">
                <h3>İşlem Sayısı</h3>
                <span id="pids">0</span>
            </div>
        </div>
    </div>

    <script>
        const containerId = "{{ $id }}";
        let isConnected = true;

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function updateStats() {
            if (!isConnected) return;

            fetch(`/api/server/docker-containers/stats/${containerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.data;

                        // CPU
                        document.getElementById('cpu').textContent = stats.CPUPerc;
                        document.getElementById('cpu-bar').style.width = stats.CPUPerc;

                        // Memory
                        document.getElementById('memUsage').textContent = stats.MemUsage;
                        document.getElementById('memPerc').textContent = '(' + stats.MemPerc + ')';
                        document.getElementById('mem-bar').style.width = stats.MemPerc;

                        // Network I/O
                        document.getElementById('netIO').textContent = stats.NetIO;

                        // Block I/O
                        document.getElementById('blockIO').textContent = stats.BlockIO;

                        // PIDs
                        document.getElementById('pids').textContent = stats.PIDs || 0;

                        document.getElementById('stats-status').textContent = '✅ Canlı';
                        document.getElementById('stats-status').style.color = '#4ecdc4';
                        document.getElementById('stats-status').style.background = 'rgba(78, 205, 196, 0.2)';
                    } else {
                        throw new Error(data.message || 'İstatistik alınamadı');
                    }
                })
                .catch(error => {
                    console.error('Stats fetch error:', error);
                    document.getElementById('stats-status').textContent = '❌ Hata';
                    document.getElementById('stats-status').style.color = '#ff6b6b';
                    document.getElementById('stats-status').style.background = 'rgba(255, 107, 107, 0.2)';
                });
        }


        updateStats();


        const interval = setInterval(updateStats, 2000);


        window.addEventListener('beforeunload', () => {
            isConnected = false;
            clearInterval(interval);
        });


        window.addEventListener('message', (event) => {
            if (event.data === 'close') {
                isConnected = false;
                clearInterval(interval);
            }
        });
    </script>
</body>
</html>
