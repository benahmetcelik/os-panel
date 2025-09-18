echo "📥 Cloning the panel repository..."
git clone https://github.com/benahmetcelik/os-panel.git /tmp/panel-temp

echo "📋 Copying files..."
cp -r /tmp/panel-temp/* /var/www/panel/
cp -r /tmp/panel-temp/.[^.]* /var/www/panel/ 2>/dev/null || true

echo "🧹 Cleaning up temporary files..."
rm -rf /tmp/panel-temp

echo "🔒 Adding git safe directory..."
git config --global --add safe.directory /var/www/panel

echo "🗄️  Creating database file..."
cd /var/www/panel || exit
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite
echo "✅ SQLite database created."

echo "⚙️  Setting up environment file..."
if [ -f ".env.example" ]; then
    cp .env.example .env
    echo "✅ .env file created from .env.example"
else
    echo "⚠️  Warning: .env.example not found!"
fi

echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "🔑 Setting up Laravel..."
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:batches-table
php artisan queue:table

echo "🗄️  Running database migrations..."
php artisan migrate --seed --force

echo "🔗 Creating storage link..."
php artisan storage:link

echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/panel
chmod -R 755 /var/www/panel
chmod -R 775 /var/www/panel/storage
chmod -R 775 /var/www/panel/bootstrap/cache
echo "🔐 Final permission check..."
chown -R www-data:www-data /var/www/panel
chmod -R 755 /var/www/panel/storage /var/www/panel/bootstrap/cache
