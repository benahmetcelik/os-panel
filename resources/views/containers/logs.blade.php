<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Container Logs - {{ $id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #1a1a1a;
            color: #0f0;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            line-height: 1.4;
            overflow: hidden;
        }

        .log-header {
            background: #2d2d2d;
            padding: 10px 15px;
            border-bottom: 1px solid #444;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .log-title {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        .log-status {
            color: #0f0;
            font-size: 12px;
        }

        #log-container {
            background: #000;
            color: #0f0;
            padding: 15px;
            height: calc(100vh - 60px);
            overflow: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 12px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .log-line {
            margin-bottom: 2px;
        }

        .log-error {
            color: #ff6b6b;
        }

        .log-warning {
            color: #ffd93d;
        }

        .log-info {
            color: #4ecdc4;
        }

        .loading {
            color: #666;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            padding: 20px;
        }

        /* Scrollbar stilleri */
        #log-container::-webkit-scrollbar {
            width: 8px;
        }

        #log-container::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        #log-container::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 4px;
        }

        #log-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="log-header">
        <div class="log-title">📄 Container Logları - {{ $id }}</div>
        <div class="log-status" id="log-status">🔄 Yükleniyor...</div>
    </div>

    <pre id="log-container">
        <div class="loading">Loglar yükleniyor...</div>
    </pre>

    <script>
        const containerId = "{{ $id }}";
        const logContainer = document.getElementById('log-container');
        const logStatus = document.getElementById('log-status');
        let isConnected = true;

        async function fetchLogs() {
            if (!isConnected) return;

            try {
                const response = await fetch(`/api/server/docker-containers/logs/${containerId}`);
                const data = await response.json();

                if (data.success && data.logs) {
                    const logs = data.logs;
                    logContainer.textContent = logs;
                    logContainer.scrollTop = logContainer.scrollHeight;
                    logStatus.textContent = '✅ Canlı';
                    logStatus.style.color = '#0f0';
                } else {
                    throw new Error(data.message || 'Log alınamadı');
                }
            } catch (err) {
                console.error('Failed to fetch logs:', err);
                logStatus.textContent = '❌ Bağlantı hatası';
                logStatus.style.color = '#ff6b6b';

                if (logContainer.textContent.includes('Loglar yükleniyor')) {
                    logContainer.innerHTML = '<div class="error">Loglar yüklenemedi: ' + err.message + '</div>';
                }
            }
        }


        fetchLogs();


        const interval = setInterval(fetchLogs, 3000);


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
