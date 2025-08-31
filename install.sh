touch database/database.sqlite
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
echo "Installation complete. Please configure your .env file as needed."
bash start-terminal-server.sh

