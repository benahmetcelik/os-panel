#!/bin/bash

set -e

echo "OS Panel Kurulum Scripti"
echo "========================"

if [ "$EUID" -ne 0 ]; then
    echo "Bu script root yetkisiyle çalıştırılmalı. 'sudo' kullanın."
    exit 1
fi




echo "🖥️  Starting directory setter script..."
chmod +x bash_files/directory_setter.sh
bash bash_files/directory_setter.sh


echo "🖥️  Starting updater script..."
chmod +x bash_files/updater.sh
bash bash_files/updater.sh


echo "🖥️  Starting git installer script..."
chmod +x bash_files/git_installer.sh
bash bash_files/git_installer.sh


echo "🖥️  Starting php installer script..."
chmod +x bash_files/php_installer.sh
bash bash_files/php_installer.sh

echo "🖥️  Starting composer installer script..."
chmod +x bash_files/composer.sh
bash bash_files/composer.sh

echo "🖥️  Starting nginx installer script..."
chmod +x bash_files/nginx_installer.sh
bash bash_files/nginx_installer.sh

echo "🖥️  Starting os panel installer script..."
chmod +x bash_files/os_panel_installer.sh
bash bash_files/os_panel_installer.sh

echo "🖥️  Starting sudoers script..."
chmod +x bash_files/sudoers.sh
bash bash_files/sudoers.sh

echo "🖥️  Starting os panel nginx installer script..."
chmod +x bash_files/os_panel_nginx_installer.sh
bash bash_files/os_panel_nginx_installer.sh


echo "🔐 SSL Module Installing..."
sudo apt install certbot python3-certbot-nginx -y

echo "🖥️  Starting terminal server script..."
chmod +x bash_files/start-terminal-server.sh
bash bash_files/start-terminal-server.sh

echo "🖥️  Starting final screen script..."
chmod +x bash_files/final_screen.sh
bash bash_files/final_screen.sh
