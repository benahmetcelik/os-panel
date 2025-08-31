<!DOCTYPE html>
<html>
<head>
    <title>Basit Docker Terminal Test</title>
    <style>
        body {
            background: #1a1a1a;
            color: #fff;
            font-family: monospace;
            margin: 20px;
        }
        #log {
            width: 100%;
            height: 400px;
            background: #000;
            border: 1px solid #333;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        #command {
            width: 80%;
            background: #000;
            color: #0f0;
            border: 1px solid #333;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        button {
            background: #333;
            color: #fff;
            border: 1px solid #555;
            padding: 10px 20px;
            margin-left: 10px;
            cursor: pointer;
        }
        button:hover {
            background: #555;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .connected { background: #2d5a2d; }
        .disconnected { background: #5a2d2d; }
    </style>
</head>
<body>
    <h1>🔧 Docker Terminal Test - Container: {{ $containerId }}</h1>

    <div id="status" class="status disconnected">
        🔴 WebSocket Bağlantısı: Bağlanıyor...
    </div>

    <div id="log"></div>

    <div>
        <input type="text" id="command" placeholder="Komut girin (örn: ls -la)" />
        <button onclick="sendCommand()">Gönder</button>
        <button onclick="clearLog()">Log Temizle</button>
        <button onclick="testConnection()">Bağlantı Test Et</button>
    </div>

    <script>
        const logDiv = document.getElementById('log');
        const commandInput = document.getElementById('command');
        const statusDiv = document.getElementById('status');
        const ws = new WebSocket('ws://localhost:8080');
        let isConnected = false;

        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                'info': '#339af0',
                'success': '#51cf66',
                'error': '#ff6b6b',
                'warning': '#ffd43b'
            };
            const color = colors[type] || '#fff';

            logDiv.innerHTML += `<div style="color: ${color}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function updateStatus(connected) {
            isConnected = connected;
            if (connected) {
                statusDiv.className = 'status connected';
                statusDiv.innerHTML = '🟢 WebSocket Bağlantısı: Bağlı';
            } else {
                statusDiv.className = 'status disconnected';
                statusDiv.innerHTML = '🔴 WebSocket Bağlantısı: Bağlantı Yok';
            }
        }

        function sendCommand() {
            if (!isConnected) {
                log('WebSocket bağlantısı yok!', 'error');
                return;
            }

            const command = commandInput.value.trim();
            if (!command) {
                log('Lütfen bir komut girin!', 'warning');
                return;
            }

            log(`Komut gönderiliyor: ${command}`, 'info');

            const message = {
                containerId: '{{ $containerId }}',
                input: command + '\n'
            };

            log(`WebSocket mesajı: ${JSON.stringify(message)}`, 'info');
            ws.send(JSON.stringify(message));

            commandInput.value = '';
        }

        function clearLog() {
            logDiv.innerHTML = '';
        }

        function testConnection() {
            log('Bağlantı testi başlatılıyor...', 'info');
            if (isConnected) {
                log('WebSocket bağlı - test mesajı gönderiliyor', 'info');
                ws.send(JSON.stringify({
                    containerId: '{{ $containerId }}',
                    input: 'echo "Test bağlantısı başarılı!"\n'
                }));
            } else {
                log('WebSocket bağlantısı yok!', 'error');
            }
        }


        ws.onopen = () => {
            log('WebSocket bağlantısı kuruldu!', 'success');
            updateStatus(true);
        };

        ws.onmessage = (event) => {
            log(`Ham mesaj alındı: ${event.data}`, 'info');

            try {
                const message = JSON.parse(event.data);
                log(`JSON parse edildi: ${JSON.stringify(message)}`, 'info');

                if (message.error) {
                    log(`Hata: ${message.error}`, 'error');
                } else if (message.type === 'output') {
                    log(`Çıktı: ${message.data}`, 'success');
                } else if (message.type === 'error') {
                    log(`Stderr: ${message.data}`, 'error');
                } else {
                    log(`Bilinmeyen mesaj tipi: ${JSON.stringify(message)}`, 'warning');
                }
            } catch (e) {
                log(`JSON parse hatası: ${e.message}`, 'error');
                log(`Ham veri: ${event.data}`, 'error');
            }
        };

        ws.onclose = () => {
            log('WebSocket bağlantısı kapandı!', 'error');
            updateStatus(false);
        };

        ws.onerror = (error) => {
            log(`WebSocket hatası: ${error}`, 'error');
            updateStatus(false);
        };


        commandInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendCommand();
            }
        });


        log('Docker Terminal Test başlatıldı', 'info');
        log(`Container ID: {{ $containerId }}`, 'info');
        log('WebSocket bağlantısı bekleniyor...', 'info');
    </script>
</body>
</html>
