#!/bin/bash

set -e

echo "OS Panel Kurulum Scripti"
echo "========================"

if [ "$EUID" -ne 0 ]; then
    echo "Bu script root yetkisiyle çalıştırılmalı. 'sudo' kullanın."
    exit 1
fi

echo "🖥️  Starting directory setter script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/directory_setter.sh" | bash

echo "📂 Changing to /var/www/panel directory..."
cd /var/www/panel

echo "🖥️  Starting updater script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/updater.sh" | bash

echo "🖥️  Starting git installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/git_installer.sh" | bash

echo "🖥️  Starting php installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/php_installer.sh" | bash

echo "🖥️  Starting composer installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/composer.sh" | bash

echo "🖥️  Starting nginx installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/nginx_installer.sh" | bash

echo "🖥️  Starting os panel installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/os_panel_installer.sh" | bash

echo "🖥️  Starting sudoers script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/sudoers.sh" | bash

echo "🖥️  Starting os panel nginx installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/os_panel_nginx_installer.sh" | bash

echo "🔐 SSL Module Installing..."
sudo apt install certbot python3-certbot-nginx -y

echo "🖥️  Starting redis installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/redis_installer.sh" | bash

echo "🖥️  Starting supervisor installer script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/supervisor_installer.sh" | bash

echo "🖥️  Starting supervisor setup script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/supervisor_setup_script.sh" | bash

echo "🖥️  Starting terminal server script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/start-terminal-server.sh" | bash

echo "🖥️  Starting final screen script..."
curl "https://raw.githubusercontent.com/benahmetcelik/os-panel/refs/heads/main/bash_files/final_screen.sh" | bash
