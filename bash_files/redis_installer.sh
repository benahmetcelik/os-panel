#!/bin/bash
echo "🧩 Installing Redis..."

if ! command -v redis-server &> /dev/null; then
    apt update
    apt install -y redis-server

    systemctl enable redis-server
    systemctl start redis-server

    echo "✅ Redis installed and started."
else
    echo "✅ Redis already installed."
fi
