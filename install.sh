# var/www/panel klasörü var mı kontrol et
if [ ! -d "/var/www/panel" ]; then
  echo "Warning: /var/www/panel directory does not exist."
    mkdir -p /var/www/panel
    echo "/var/www/panel directory created."
else
    echo "/var/www/panel directory exists."
    echo "Cancel (0) or Continue (1)?"
    echo "If you choose to continue, the installation may deleting existing files."
    echo -n "Enter your choice: "
    read choice
    if [ "$choice" -eq 0 ]; then
        echo "Exiting installation."
        exit 1
    else
        echo "Continuing installation..."
        rm -rf /var/www/panel/*
    fi
fi
echo "Changing to /var/www/panel directory..."
cd /var/www/panel || exit

echo "Updating package lists..."
apt update

echo "Upgrading installed packages..."
apt upgrade -y

echo "Installing required packages..."
apt install php php-fpm php-sqlite3 php-cli php-curl php-mbstring php-xml php-zip
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
echo "Installing Nginx..."
apt install -y nginx
apt install nginx-core
echo "Starting Nginx service..."
systemctl start nginx
systemctl enable nginx

echo "Creating Nginx configuration for the panel..."
cat > /etc/nginx/sites-available/panel.conf <<EOL
server {
    listen 80;
    server_name _;  # Your server ip address or domain

    root /var/www/panel/public/;
    index index.php index.html index.htm;

    access_log /var/log/nginx/panel.access.log;
    error_log /var/log/nginx/panel.error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOL

echo "Enabling Nginx site configuration..."
ln -s /etc/nginx/sites-available/panel.conf /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
echo "Installing PHP-FPM..."
apt install -y php-fpm
echo "Starting PHP-FPM service..."
systemctl start php8.1-fpm
systemctl enable php8.1-fpm
echo "Cloning the panel repository..."
git clone https://github.com/benahmetcelik/os-panel.git .

echo "Adding git"
git config --global --add safe.directory /var/www/panel


echo "Setting permissions..."
chown -R www-data:www-data /var/www/panel
chmod -R 755 /var/www/panel/storage
chmod -R 755 /var/www/panel/bootstrap/cache
echo "Setting up the application..."

touch database/database.sqlite
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite
copy .env.example .env
composer install
php artisan optimize
php artisan optimize:clear
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
echo "Installation complete. Please configure your .env file as needed."
bash start-terminal-server.sh

