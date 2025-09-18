#!/bin/bash
echo "🛠 Installing Supervisor..."

if ! command -v supervisord &> /dev/null; then
    apt update
    apt install -y supervisor

    systemctl enable supervisor
    systemctl start supervisor

    echo "✅ Supervisor installed and started."
else
    echo "✅ Supervisor already installed."
fi
