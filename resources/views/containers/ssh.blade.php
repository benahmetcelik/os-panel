<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker Terminal - {{ $containerId }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            color: #0f0;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            height: 100vh;
            overflow: hidden;
        }

        .terminal-container {
            height: 100vh;
            background: #000;
            display: flex;
            flex-direction: column;
        }

        .terminal-header {
            background: #1a1a1a;
            padding: 8px 15px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .terminal-title {
            font-weight: 600;
            color: #0f0;
            font-size: 13px;
        }

        .connection-status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }

        .connected {
            background: #1a4a1a;
            color: #0f0;
        }

        .disconnected {
            background: #4a1a1a;
            color: #ff6b6b;
        }

        #terminal {
            flex: 1;
            background: #000;
        }

        /* Xterm.js stilleri */
        .xterm {
            font-size: 13px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            padding: 10px;
        }

        .xterm-viewport {
            background: #000 !important;
        }

        .xterm-screen {
            background: #000 !important;
        }

        /* Parent sayfa ile iletişim için */
        .iframe-mode {
            border: none;
            outline: none;
        }
    </style>


    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-web-links@0.9.0/lib/xterm-addon-web-links.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" />
</head>
<body>
    <div class="terminal-container">
        <div class="terminal-header">
            <div class="terminal-title">
                🐳 {{ $containerId }}
            </div>
            <div id="status" class="connection-status disconnected">
                🔴 Bağlanıyor...
            </div>
        </div>

        <div id="terminal"></div>
    </div>

    <script>

        const containerId = "{{ $containerId }}";
        let currentDirectory = "/";
        let currentCommand = "";
        let isConnected = false;


        const term = new Terminal({
            cursorBlink: true,
            theme: {
                background: '#000000',
                foreground: '#00ff00',
                cursor: '#00ff00',
                selection: '#ffffff',
                black: '#000000',
                red: '#ff0000',
                green: '#00ff00',
                yellow: '#ffff00',
                blue: '#0000ff',
                magenta: '#ff00ff',
                cyan: '#00ffff',
                white: '#ffffff',
                brightBlack: '#666666',
                brightRed: '#ff6666',
                brightGreen: '#66ff66',
                brightYellow: '#ffff66',
                brightBlue: '#6666ff',
                brightMagenta: '#ff66ff',
                brightCyan: '#66ffff',
                brightWhite: '#ffffff'
            },
            fontFamily: 'Monaco, Menlo, Ubuntu Mono, monospace',
            fontSize: 13,
            scrollback: 1000,
            cols: 80,
            rows: 24
        });


        term.open(document.getElementById('terminal'));


        const fitAddon = new FitAddon.FitAddon();
        term.loadAddon(fitAddon);
        fitAddon.fit();


        const webLinksAddon = new WebLinksAddon.WebLinksAddon();
        term.loadAddon(webLinksAddon);


        function updateStatus(status, message) {
            const statusElement = document.getElementById('status');
            statusElement.textContent = message;
            statusElement.className = `connection-status ${status}`;
        }


        function writePrompt() {
            term.write(`\r\n${currentDirectory}$ \x1B[0m`);
        }


        const ws = new WebSocket('ws://localhost:8080');

        ws.onopen = function() {
            updateStatus('connected', '🟢 Bağlandı');
            isConnected = true;
            term.write('\x1B[1;32m[WebSocket bağlantısı kuruldu]\x1B[0m\r\n');
            writePrompt();
        };

        ws.onmessage = function(event) {
            try {
                const message = JSON.parse(event.data);

                if (message.type === 'output') {

                    if (message.data) {

                        let output = message.data.replace(/\n/g, '\r\n');
                        term.write('\r\n' + output);
                    }
                    writePrompt();
                } else if (message.type === 'error') {
                    term.write('\r\n\x1B[1;31m[Hata] ' + message.data + '\x1B[0m\r\n');
                    writePrompt();
                } else if (message.type === 'directory') {
                    currentDirectory = message.data;
                    term.write('\r\n\x1B[1;34m[Dizin değiştirildi: ' + currentDirectory + ']\x1B[0m\r\n');
                    writePrompt();
                }
            } catch (e) {
                console.error('JSON parse error:', e);
            }
        };

        ws.onclose = function() {
            updateStatus('disconnected', '🔴 Bağlantı kesildi');
            isConnected = false;
            term.write('\r\n\x1B[1;31m[WebSocket bağlantısı kesildi]\x1B[0m\r\n');
        };

        ws.onerror = function(error) {
            updateStatus('disconnected', '🔴 Bağlantı hatası');
            isConnected = false;
            term.write('\r\n\x1B[1;31m[WebSocket bağlantı hatası]\x1B[0m\r\n');
        };


        term.onData(function(data) {
            if (!isConnected) return;

            if (data === '\r') {
                if (currentCommand.trim()) {
                    term.write('\r\n');


                    const commandData = {
                        containerId: containerId,
                        input: currentCommand
                    };

                    ws.send(JSON.stringify(commandData));
                    currentCommand = "";
                } else {
                    writePrompt();
                }
            } else if (data === '\u007F') {
                if (currentCommand.length > 0) {
                    currentCommand = currentCommand.slice(0, -1);
                    term.write('\b \b');
                }
            } else if (data >= ' ') {
                currentCommand += data;
                term.write(data);
            }
        });


        window.addEventListener('resize', function() {
            fitAddon.fit();
        });


        window.addEventListener('message', function(event) {
            if (event.data === 'close') {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.close();
                }
            }
        });


        window.addEventListener('beforeunload', function() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.close();
            }
        });
    </script>
</body>
</html>
