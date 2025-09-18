source ./vars.sh

echo "Target PHP version: $PHP_VERSION"

# Ubuntu/Debian için PHP 8.1 repository ekle
echo "🔧 Adding PHP 8.1 repository..."
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

echo "🐘 Installing PHP 8.1 and required extensions..."
apt install -y \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl

# PHP 8.1'i varsayılan yap
echo "⚙️  Setting PHP 8.1 as default..."
update-alternatives --set php /usr/bin/php${PHP_VERSION}

# PHP versiyonunu doğrula
CURRENT_PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "✅ Current PHP version: $CURRENT_PHP_VERSION"

if [ "$CURRENT_PHP_VERSION" != "$PHP_VERSION" ]; then
    echo "❌ ERROR: PHP version mismatch. Expected $PHP_VERSION, got $CURRENT_PHP_VERSION"
    exit 1
fi

echo "🐘 Starting PHP-FPM service..."
systemctl start php${PHP_VERSION}-fpm
systemctl enable php${PHP_VERSION}-fpm
