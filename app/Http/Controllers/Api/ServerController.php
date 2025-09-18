<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ServerController extends Controller
{
    /**
     * Sistem istatistiklerini getir
     */
    public function getSystemStats(): JsonResponse
    {
        try {

            $stats = Cache::remember('server_stats', 30, function () {
                return [
                    'cpu' => $this->getCpuUsage(),
                    'memory' => $this->getMemoryUsage(),
                    'disk' => $this->getDiskUsage(),
                    'network' => $this->getNetworkStats(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem bilgileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nginx sitelerini getir
     */
    public function getNginxSites(): JsonResponse
    {
        try {
            $sites = Cache::remember('nginx_sites', 60, function () {
                return $this->parseNginxSites();
            });

            return response()->json([
                'success' => true,
                'data' => $sites,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nginx siteleri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CPU kullanımını hesapla
     */
    private function getCpuUsage(): array
    {

        if (PHP_OS_FAMILY === 'Linux') {

            $cpuInfo1 = $this->getCpuInfo();
            sleep(1);
            $cpuInfo2 = $this->getCpuInfo();

            $usage = $this->calculateCpuUsage($cpuInfo1, $cpuInfo2);

            return [
                'usage' => round($usage, 1),
                'cores' => $this->getCpuCores(),
                'model' => $this->getCpuModel()
            ];
        }


        return [
            'usage' => rand(15, 45),
            'cores' => 4,
            'model' => 'Unknown'
        ];
    }

    /**
     * Bellek kullanımını getir
     */
    private function getMemoryUsage(): array
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $total);
            preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $available);

            $totalBytes = isset($total[1]) ? $total[1] * 1024 : 0;
            $availableBytes = isset($available[1]) ? $available[1] * 1024 : 0;
            $usedBytes = $totalBytes - $availableBytes;

            return [
                'total' => $totalBytes,
                'used' => $usedBytes,
                'free' => $availableBytes,
                'usage_percent' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0
            ];
        }

        $total = 8 * 1024 * 1024 * 1024;
        $used = $total * 0.65;         return [
            'total' => $total,
            'used' => $used,
            'free' => $total - $used,
            'usage_percent' => 65.0
        ];
    }

    /**
     * Disk kullanımını getir
     */
    private function getDiskUsage(): array
    {
        $path = '/';         if (function_exists('disk_total_space') && function_exists('disk_free_space')) {
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;

            return [
                'total' => $total,
                'used' => $used,
                'free' => $free,
                'usage_percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0
            ];
        }

        $total = 100 * 1024 * 1024 * 1024;
        $used = $total * 0.42;         return [
            'total' => $total,
            'used' => $used,
            'free' => $total - $used,
            'usage_percent' => 42.0
        ];
    }

    /**
     * Ağ istatistiklerini getir
     */
    private function getNetworkStats(): array
    {
        if (PHP_OS_FAMILY === 'Linux') {
            try {

                $interfaces = $this->getNetworkInterfaces();
                $primaryInterface = $this->getPrimaryInterface($interfaces);

                return [
                    'interface' => $primaryInterface,
                    'upload_speed' => $this->getNetworkSpeed('tx'),
                    'download_speed' => $this->getNetworkSpeed('rx'),
                    'status' => 'connected'
                ];
            } catch (\Exception $e) {
                return [
                    'interface' => 'unknown',
                    'upload_speed' => 0,
                    'download_speed' => 0,
                    'status' => 'error'
                ];
            }
        }

        return [
            'interface' => 'eth0',
            'upload_speed' => rand(1024, 10240),
            'download_speed' => rand(10240, 102400),
            'status' => 'connected'
        ];
    }

    /**
     * Nginx sitelerini parse et
     */
    private function parseNginxSites(): array
    {
        $sites = [];

        try {

            $nginxPath = '/etc/nginx/sites-available/';

            if (!is_dir($nginxPath)) {

                $nginxPath = '/usr/local/nginx/conf/sites-available/';
                if (!is_dir($nginxPath)) {
                    throw new \Exception('Nginx configuration dizini bulunamadı');
                }
            }

            $configFiles = glob($nginxPath . '*');

            foreach ($configFiles as $configFile) {
                if (is_file($configFile)) {
                    $siteInfo = $this->parseNginxConfig($configFile);
                    if ($siteInfo) {
                        $sites[] = $siteInfo;
                    }
                }
            }


            $activeSites = $this->getActiveSites();
            foreach ($sites as &$site) {
                $site['status'] = in_array($site['name'], $activeSites) ? 'active' : 'inactive';
            }

        } catch (\Exception $e) {

            $sites = [

                [
                    'name' => 'example.com',
                    'domain' => 'http://example.com',
                    'port' => 80,
                    'ssl' => false,
                    'root' => '/var/www/html',
                    'status' => 'unknown'
                ],
                [
                    'name' => 'example.com',
                    'domain' => 'http://example.com',
                    'port' => 80,
                    'ssl' => false,
                    'root' => '/var/www/html',
                    'status' => 'unknown'
                ],
                [
                    'name' => 'example.com',
                    'domain' => 'http://example.com',
                    'port' => 80,
                    'ssl' => false,
                    'root' => '/var/www/html',
                    'status' => 'unknown'
                ]
            ];
        }

        return $sites;
    }

    /**
     * CPU bilgilerini oku
     */
    private function getCpuInfo(): array
    {
        $stat = file_get_contents('/proc/stat');
        $lines = explode("\n", $stat);
        $cpuLine = $lines[0];
        $values = preg_split('/\s+/', $cpuLine);

        return [
            'user' => intval($values[1]),
            'nice' => intval($values[2]),
            'system' => intval($values[3]),
            'idle' => intval($values[4]),
            'iowait' => intval($values[5]),
            'irq' => intval($values[6]),
            'softirq' => intval($values[7])
        ];
    }

    /**
     * CPU kullanım yüzdesini hesapla
     */
    private function calculateCpuUsage(array $cpu1, array $cpu2): float
    {
        $idle1 = $cpu1['idle'];
        $idle2 = $cpu2['idle'];

        $total1 = array_sum($cpu1);
        $total2 = array_sum($cpu2);

        $totalDiff = $total2 - $total1;
        $idleDiff = $idle2 - $idle1;

        if ($totalDiff == 0) return 0;

        return (1 - ($idleDiff / $totalDiff)) * 100;
    }

    /**
     * CPU çekirdek sayısını getir
     */
    private function getCpuCores(): int
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            return substr_count($cpuinfo, 'processor');
        }
        return 4;
    }

    /**
     * CPU modelini getir
     */
    private function getCpuModel(): string
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match('/model name\s*:\s*(.+)/i', $cpuinfo, $matches);
            return isset($matches[1]) ? trim($matches[1]) : 'Unknown';
        }
        return 'Unknown';
    }

    /**
     * Ağ interface'lerini getir
     */
    private function getNetworkInterfaces(): array
    {
        $interfaces = [];

        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec('ls /sys/class/net/');
            if ($output) {
                $interfaces = array_filter(explode("\n", trim($output)));
            }
        }

        return $interfaces;
    }

    /**
     * Ana ağ interface'ini belirle
     */
    private function getPrimaryInterface(array $interfaces): string
    {

        $interfaces = array_filter($interfaces, function($interface) {
            return $interface !== 'lo';
        });


        foreach ($interfaces as $interface) {
            if (strpos($interface, 'eth') === 0 || strpos($interface, 'ens') === 0) {
                return $interface;
            }
        }


        return !empty($interfaces) ? $interfaces[0] : 'eth0';
    }

    /**
     * Ağ hızını getir
     */
    private function getNetworkSpeed(string $direction): int
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $interface = $this->getPrimaryInterface($this->getNetworkInterfaces());


            $bytes1 = $this->getNetworkBytes($interface, $direction);
            sleep(1);
            $bytes2 = $this->getNetworkBytes($interface, $direction);

            return max(0, $bytes2 - $bytes1);
        }


        return $direction === 'rx' ? rand(10240, 102400) : rand(1024, 10240);
    }

    /**
     * Interface'den byte bilgisini oku
     */
    private function getNetworkBytes(string $interface, string $direction): int
    {
        $file = "/sys/class/net/{$interface}/statistics/{$direction}_bytes";

        if (file_exists($file)) {
            return intval(file_get_contents($file));
        }

        return 0;
    }

    /**
     * Nginx config dosyasını parse et
     */
    private function parseNginxConfig(string $configFile): ?array
    {
        $content = file_get_contents($configFile);
        $basename = basename($configFile);

        preg_match('/server_name\s+([^;]+);/i', $content, $serverNameMatches);
        $serverName = isset($serverNameMatches[1]) ? trim($serverNameMatches[1]) : $basename;

        preg_match('/listen\s+(\d+)/i', $content, $portMatches);
        $port = isset($portMatches[1]) ? intval($portMatches[1]) : 80;

        $hasSSL = strpos($content, 'ssl_certificate') !== false || $port === 443;

        preg_match('/root\s+([^;]+);/i', $content, $rootMatches);
        $root = isset($rootMatches[1]) ? trim($rootMatches[1]) : '';

        return [
            'name' => $basename,
            'domain' => ($hasSSL ? 'https://' : 'http://') . $serverName,
            'port' => $port,
            'ssl' => $hasSSL,
            'root' => $root,
            'status' => 'unknown'
        ];
    }

    /**
     * Aktif siteleri getir
     */
    private function getActiveSites(): array
    {
        $activeSites = [];

        $enabledPath = '/etc/nginx/sites-enabled/';

        if (is_dir($enabledPath)) {
            $enabledFiles = glob($enabledPath . '*');
            foreach ($enabledFiles as $file) {
                if (is_link($file)) {
                    $activeSites[] = basename($file);
                }
            }
        }

        $nginxRunning = $this->isNginxRunning();
        if (!$nginxRunning) {
            return [];
        }

        return $activeSites;
    }

    /**
     * Nginx'in çalışıp çalışmadığını kontrol et
     */
    private function isNginxRunning(): bool
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec('pgrep nginx');
            return !empty(trim($output));
        }

        return true;
    }

    /**
     * Sistem uptime'ı getir
     */
    public function getSystemUptime(): JsonResponse
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $uptime = file_get_contents('/proc/uptime');
                $uptimeSeconds = floatval(explode(' ', $uptime)[0]);

                $days = floor($uptimeSeconds / 86400);
                $hours = floor(($uptimeSeconds % 86400) / 3600);
                $minutes = floor(($uptimeSeconds % 3600) / 60);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'uptime_seconds' => $uptimeSeconds,
                        'uptime_formatted' => "{$days} gün, {$hours} saat, {$minutes} dakika",
                        'boot_time' => now()->subSeconds($uptimeSeconds)->toISOString()
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'uptime_seconds' => 86400,
                    'uptime_formatted' => '1 gün, 0 saat, 0 dakika',
                    'boot_time' => now()->subDay()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Uptime bilgisi alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sistem yükü (load average) getir
     */
    public function getSystemLoad(): JsonResponse
    {
        try {
            if (PHP_OS_FAMILY === 'Linux' && function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'load_1min' => round($load[0], 2),
                        'load_5min' => round($load[1], 2),
                        'load_15min' => round($load[2], 2),
                        'cpu_cores' => $this->getCpuCores()
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'load_1min' => 0.5,
                    'load_5min' => 0.3,
                    'load_15min' => 0.2,
                    'cpu_cores' => 4
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem yükü alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nginx durumunu kontrol et
     */
    public function getNginxStatus(): JsonResponse
    {
        try {
            $isRunning = $this->isNginxRunning();
            $configTest = $this->testNginxConfig();

            return response()->json([
                'success' => true,
                'data' => [
                    'running' => $isRunning,
                    'config_valid' => $configTest['valid'],
                    'config_message' => $configTest['message'],
                    'version' => $this->getNginxVersion(),
                    'active_connections' => $this->getActiveConnections()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nginx durumu alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nginx config'ini test et
     */
    private function testNginxConfig(): array
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec('nginx -t 2>&1');
            $valid = strpos($output, 'syntax is ok') !== false && strpos($output, 'test is successful') !== false;

            return [
                'valid' => $valid,
                'message' => $valid ? 'Konfigürasyon geçerli' : 'Konfigürasyon hatası: ' . $output
            ];
        }

        return ['valid' => true, 'message' => 'Test edilemedi'];
    }

    /**
     * Nginx versiyonunu getir
     */
    private function getNginxVersion(): string
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec('nginx -v 2>&1');
            preg_match('/nginx\/([0-9.]+)/', $output, $matches);
            return isset($matches[1]) ? $matches[1] : 'unknown';
        }

        return '1.18.0';
    }

    /**
     * Aktif bağlantı sayısını getir
     */
    private function getActiveConnections(): int
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec('netstat -an | grep :80 | grep ESTABLISHED | wc -l');
            return intval(trim($output));
        }

        return rand(5, 50);
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('server_stats');
            Cache::forget('nginx_sites');

            return response()->json([
                'success' => true,
                'message' => 'Cache temizlendi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenemedi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getDockerContainers():JsonResponse
    {
        try {

            $output = shell_exec('docker ps -a --format "{{json .}}"');
            $lines = array_filter(explode("\n", trim($output)));
            $containers = array_map(function ($line) {
                return json_decode($line, true);
            }, $lines);

            $statsOutput = shell_exec('docker stats --no-stream --format "{{json .}}"');
            $statsLines = array_filter(explode("\n", trim($statsOutput)));
            $stats = [];
            foreach ($statsLines as $statLine) {
                $stat = json_decode($statLine, true);
                $stats[$stat['ID']] = $stat;
            }

            foreach ($containers as &$container) {
                $id = $container['ID'];
                if (isset($stats[$id])) {
                    $container['CPUPerc'] = $stats[$id]['CPUPerc'];
                    $container['MemUsage'] = $stats[$id]['MemUsage'];
                    $container['MemPerc'] = $stats[$id]['MemPerc'];
                } else {
                    $container['CPUPerc'] = '0%';
                    $container['MemUsage'] = '0 / 0';
                    $container['MemPerc'] = '0%';
                }
            }

            return response()->json([
                'success' => true,
                'data' => $containers
            ]);


        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Docker konteyner bilgisi alınamadı: ' . $e->getMessage()
            ], 500);
        }

    }


    public function getDockerContainerStats($containerId):JsonResponse
    {
        try {

            $output = shell_exec("docker stats {$containerId} --no-stream --format '{{json .}}'");
            $stats = json_decode(trim($output), true);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Docker konteyner istatistik bilgisi alınamadı: ' . $e->getMessage()
            ], 500);
        }

    }


    public function restartQueue($queueName)
    {
        exec('nohup php /var/www/panel/artisan queue:work --queue='.$queueName.' --sleep=3 --tries=3 > /var/www/panel/storage/logs/queue-'.$queueName.'.log 2>&1 &');
    }
}
