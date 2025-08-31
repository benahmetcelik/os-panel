#!/bin/bash

set -e

echo "OS Panel Kurulum Scripti"
echo "========================"

if [ "$EUID" -ne 0 ]; then
    echo "Bu script root yetkisiyle çalıştırılmalı. 'sudo' kullanın."
    exit 1
fi

PHP_VERSION="8.1"
echo "Target PHP version: $PHP_VERSION"

if [ ! -d "/var/www/panel" ]; then
    echo "Warning: /var/www/panel directory does not exist."
    mkdir -p /var/www/panel
    echo "/var/www/panel directory created."
else
    echo "/var/www/panel directory exists."
    echo "Cancel (0) or Continue (1)?"
    echo "If you choose to continue, the installation may delete existing files."
    echo -n "Enter your choice: "
    read choice
    if [ "$choice" -eq 0 ]; then
        echo "Exiting installation."
        exit 1
    else
        echo "Continuing installation..."
        rm -rf /var/www/panel/*
        rm -rf /var/www/panel/.[^.]*
    fi
fi

echo "Changing to /var/www/panel directory..."
cd /var/www/panel || exit

echo "Updating package lists..."
apt update

echo "Upgrading installed packages..."
apt upgrade -y

echo "Installing Git if not present..."
if ! command -v git &> /dev/null; then
    apt install -y git
fi

# Ubuntu/Debian için PHP 8.1 repository ekle
echo "Adding PHP 8.1 repository..."
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

echo "Installing PHP 8.1 and required extensions..."
apt install -y \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-json \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-gd

# PHP 8.1'i varsayılan yap
echo "Setting PHP 8.1 as default..."
update-alternatives --set php /usr/bin/php${PHP_VERSION}

# PHP versiyonunu doğrula
CURRENT_PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "Current PHP version: $CURRENT_PHP_VERSION"

if [ "$CURRENT_PHP_VERSION" != "$PHP_VERSION" ]; then
    echo "ERROR: PHP version mismatch. Expected $PHP_VERSION, got $CURRENT_PHP_VERSION"
    exit 1
fi

echo "Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
else
    echo "Composer already installed."
fi

echo "Installing Nginx..."
apt install -y nginx nginx-common

echo "Starting Nginx service..."
systemctl start nginx
systemctl enable nginx

echo "Cloning the panel repository..."
git clone https://github.com/benahmetcelik/os-panel.git /tmp/panel-temp

cp -r /tmp/panel-temp/* /var/www/panel/
cp -r /tmp/panel-temp/.[^.]* /var/www/panel/ 2>/dev/null || true

rm -rf /tmp/panel-temp

echo "Adding git safe directory..."
git config --global --add safe.directory /var/www/panel

echo "Setting up environment file..."
if [ -f ".env.example" ]; then
    cp .env.example .env
else
    echo "Warning: .env.example not found!"
fi

echo "Creating database file..."
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Setting up Laravel..."
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running database migrations..."
php artisan migrate --seed --force

echo "Creating storage link..."
php artisan storage:link

echo "Setting permissions..."
chown -R www-data:www-data /var/www/panel
chmod -R 755 /var/www/panel
chmod -R 775 /var/www/panel/storage
chmod -R 775 /var/www/panel/bootstrap/cache

echo "Creating Nginx configuration for the panel..."
cat > /etc/nginx/sites-available/panel.conf <<EOL
server {
    listen 80;
    server_name _;  # Your server IP address or domain

    root /var/www/panel/public;
    index index.php index.html index.htm;

    access_log /var/log/nginx/panel.access.log;
    error_log /var/log/nginx/panel.error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.(env|git) {
        deny all;
    }
}
EOL

echo "Enabling Nginx site configuration..."
if [ -L /etc/nginx/sites-enabled/default ]; then
    unlink /etc/nginx/sites-enabled/default
fi

ln -sf /etc/nginx/sites-available/panel.conf /etc/nginx/sites-enabled/

echo "Testing Nginx configuration..."
if nginx -t; then
    systemctl reload nginx
    echo "Nginx configuration successful."
else
    echo "Nginx configuration failed!"
    exit 1
fi

echo "Starting PHP-FPM service..."
systemctl start php${PHP_VERSION}-fpm
systemctl enable php${PHP_VERSION}-fpm

echo "Final permission check..."
chown -R www-data:www-data /var/www/panel
chmod -R 755 /var/www/panel/storage /var/www/panel/bootstrap/cache

echo ""
echo "================================"
echo "Installation completed successfully!"
echo "================================"
echo "PHP Version: $(php --version | head -n 1)"
echo "Panel URL: http://$(curl -s ifconfig.me)/ || http://$(hostname -I | cut -d' ' -f1)/"
echo "Local URL: http://localhost/"
echo ""
echo "Next steps:"
echo "1. Configure your .env file if needed"
echo "2. Set up SSL certificate (recommended)"
echo "3. Configure firewall rules"
echo ""

if [ -f "start-terminal-server.sh" ]; then
    echo "Starting terminal server..."
    chmod +x start-terminal-server.sh
    bash start-terminal-server.sh
else
    echo "start-terminal-server.sh not found, skipping..."
fi
