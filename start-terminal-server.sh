#!/bin/bash

echo "🚀 Docker Terminal Server Başlatılıyor..."

# Mevcut terminal server'ı durdurur
echo "📋 Mevcut terminal server'ları kontrol ediliyor..."
pkill -f "docker:terminal-server" 2>/dev/null

# 2 saniye bekle (belki kapanması uzun sürebilri)
sleep 2

# Terminal server'ı başlat
echo "🔧 Terminal server başlatılıyor..."
php artisan docker:terminal-server &

# Server'ın başlaması için bekle
sleep 3

# Server'ın çalışıp çalışmadığını kontrol et
if pgrep -f "docker:terminal-server" > /dev/null; then
    echo "✅ Terminal server başarıyla başlatıldı!"
    echo "🌐 WebSocket: ws://localhost:8080"
    echo "🔍 Test sayfası: http://localhost:8000/test-terminal"
    echo ""
    echo "📝 Kullanım:"
    echo "1. Laravel server'ı başlatın: php artisan serve"
    echo "2. Test sayfasını açın: http://localhost:8000/test-terminal"
    echo "3. Container ID'sini girin ve komutları çalıştırın"
    echo ""
    echo "🛑 Durdurmak için: pkill -f 'docker:terminal-server'"
else
    echo "❌ Terminal server başlatılamadı!"
    exit 1;
fi
