# Docker Terminal Erişimi

Bu proje, Docker container'larına WebSocket üzerinden terminal erişimi sağlar.

## Kurulum ve Çalıştırma

### 1. Terminal Server'ı Başlatın

```bash
php artisan docker:terminal-server
```

Bu komut WebSocket server'ını `ws://localhost:8080` adresinde başlatır.

### 2. Laravel Development Server'ı Başlatın

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Test Container'ı Oluşturun (Opsiyonel)

```bash
docker run -d --name test-terminal nginx:alpine
```

### 4. Terminal Erişimi

Tarayıcınızda şu adresleri ziyaret edin:

- **Test Terminal**: http://localhost:8000/test-terminal
- **Container Terminal**: http://localhost:8000/docker/terminal/{container-id}

## Kullanım

1. Terminal sayfasını açın
2. WebSocket bağlantısı otomatik olarak kurulur
3. Komutları yazın ve Enter tuşuna basın
4. Container içindeki komutların çıktısını görün

## Özellikler

- ✅ WebSocket üzerinden real-time terminal erişimi
- ✅ Otomatik shell tespiti (bash/sh)
- ✅ Container durumu kontrolü
- ✅ Hata yönetimi
- ✅ Çoklu container desteği

## Sorun Giderme

### Container'a Bağlanamıyorsa

1. Container'ın çalıştığından emin olun:
   ```bash
   docker ps
   ```

2. Container'ın shell'ini kontrol edin:
   ```bash
   docker exec {container-id} which bash
   docker exec {container-id} which sh
   ```

3. Terminal server'ın çalıştığını kontrol edin:
   ```bash
   ps aux | grep "docker:terminal-server"
   ```

4. WebSocket bağlantısını test edin:
   ```bash
   curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" http://localhost:8080
   ```

### Yaygın Sorunlar

1. **Container çalışmıyor**: Container'ı başlatın
2. **Shell bulunamadı**: Container'da bash veya sh yüklü değil
3. **WebSocket bağlantı hatası**: Terminal server çalışmıyor
4. **Permission denied**: Docker komutları için yetki gerekli

## Güvenlik Notları

- Terminal server sadece localhost'ta çalışır
- Container ID'leri doğrulanır
- Sadece çalışan container'lara erişim sağlanır
- WebSocket bağlantıları güvenli şekilde yönetilir

## Geliştirme

### Yeni Özellikler Ekleme

1. `DockerTerminalServer.php` dosyasını düzenleyin
2. Frontend kodunu `ssh.blade.php` veya `test.blade.php` dosyalarında güncelleyin
3. Gerekirse yeni route'lar ekleyin

### Debug

Terminal server'ın loglarını görmek için:

```bash
php artisan docker:terminal-server --verbose
```

## Desteklenen Container'lar

- Alpine Linux (sh)
- Ubuntu/Debian (bash)
- CentOS/RHEL (bash)
- Diğer Linux dağıtımları

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.
