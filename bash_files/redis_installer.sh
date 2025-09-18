#!/bin/bash
echo "🧩 Installing Redis..."

if ! command -v redis-server &> /dev/null; then
    apt update
    apt install -y redis-server

    systemctl enable redis-server
    systemctl start redis-server

    echo "✅ Redis installed and started."
else
    echo "✅ Redis already installed."
fi

# shellcheck disable=SC2034
CURRENT_PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")

sudo apt install -y php${CURRENT_PHP_VERSION}-redis


sudo systemctl restart php${CURRENT_PHP_VERSION}-fpm
