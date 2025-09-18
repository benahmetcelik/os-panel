#!/bin/bash
set -e


LARAVEL_PATH="/var/www/panel"
SUPERVISOR_CONF="/etc/supervisor/conf.d/os-panel-worker.conf"

echo "⚙️  Setting up Supervisor config for Laravel ($LARAVEL_PATH)..."

# Supervisor dizinine kopyala
sudo cp "$LARAVEL_PATH/supervisor/os-panel-worker.conf" "$SUPERVISOR_CONF"

# Supervisor reload
echo "🔄 Reloading Supervisor..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart all

echo "✅ Supervisor config successfully set up!"
