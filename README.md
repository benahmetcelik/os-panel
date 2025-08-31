## Nasıl Kurulur

Öncelikle bağlılıkları yükleyin:

- Nginx
```bash
apt install nginx && sudo apt install nginx-core
```

- Php 8.1
```bash
sudo apt install php php-fpm php-mysql php-cli php-curl php-mbstring php-xml php-zip
sudo systemctl enable php8.1-fpm
sudo systemctl start php8.1-fpm
```


- Nginx Config
```bash
sudo nano /etc/nginx/sites-available/panel.conf
```
```bash
server {
    listen 80;
    server_name _;
    root /var/www/panel/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    access_log /var/log/nginx/panel_access.log;
    error_log /var/log/nginx/panel_error.log;
}
```

- Nginx Config Aktif Et
```bash
sudo ln -s /etc/nginx/sites-available/panel.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```
- Panel için gerekli dizin ve dosyaları oluşturun:
```bash
sudo mkdir -p /var/www/panel
sudo chown -R $USER:$USER /var/www/panel
git clone https://github.com/benahmetcelik/os-panel.git .
apt install composer
composer install
```

- .env dosyasını oluşturun ve yapılandırın:
```bash
cp .env.example .env
```

- Uygulama anahtarını oluşturun:
```bash
php artisan key:generate
```
- Gerekli dizinlere yazma izinleri verin:
```bash
sudo chown -R www-data:www-data /var/www/panel/storage
sudo chown -R www-data:www-data /var/www/panel/bootstrap/cache
```

- Git ayarlarını yapın:
```bash
git config --global --add safe.directory /var/www/panel
```

- Reponun en güncel halini çekin:
```bash
git pull .
```
