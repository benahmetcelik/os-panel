# Get server IP
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | cut -d' ' -f1 | tr -d ' ')
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")



echo "🔧 Cleaning Nginx site configuration..."
rm -rf /etc/nginx/sites-enabled/*
rm -rf /etc/nginx/sites-available/*
echo "✅ All sites disabled."

echo "🌐 Creating Nginx configuration for the panel..."
cat > /etc/nginx/sites-available/panel.conf <<EOL
server {
    listen 80;
    server_name ${SERVER_IP};

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


ln -sf /etc/nginx/sites-available/panel.conf /etc/nginx/sites-enabled/

echo "🧪 Testing OSPanel Nginx configuration..."
if nginx -t; then
    systemctl reload nginx
    echo "✅ Nginx configuration successful."
else
    echo "❌ Nginx configuration failed!"
    exit 1
fi


cp /var/www/panel/stubs/ngnix/default.conf /etc/nginx/sites-available/default.conf

ln -sf /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/

echo "🧪 Testing Default Nginx configuration..."
if nginx -t; then
    systemctl reload nginx
    echo "✅ Nginx configuration successful."
else
    echo "❌ Nginx configuration failed!"
    exit 1
fi
