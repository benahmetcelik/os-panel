<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'enabled',
        'working_directory',
        'backup_status',
        'ip_address',
        'port',
        'ssl_status',
    ];


    public function getBackups()
    {
        return $this->hasMany(Backup::class, 'site_id');
    }

    /**
     * @return void
     */
    public function createFolder()
    {
        $folder = $this->getSitePath();
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
    }

    /**
     * @return void
     */
    public function createNginxConfig()
    {

        $stub = file_get_contents(base_path('stubs/nginx/available.'.
            ($this->ssl_status ? '-with-ssl' : '')
            .'.stub'));
        $config = str_replace(
            ['[[domain]]', '[[path]]'],
            [$this->domain, $this->getSitePath()],
            $stub
        );
        file_put_contents(storage_path('nginx/'.$this->domain.'.conf'), $config);

    }

    public function getSitePath()
    {
        return public_path($this->working_directory);
    }

    public function deploySite($domain)
    {
        $availablePath = "/etc/nginx/sites-available/{$domain}.conf";
        $enabledPath   = "/etc/nginx/sites-enabled/{$domain}.conf";
        $storagePath   = storage_path("nginx/{$domain}.conf");

        // cp
        $process = new Process(['sudo', 'cp', $storagePath, $availablePath]);
        $process->run();

        // symlink
        $process = new Process(['sudo', 'ln', '-s', $availablePath, $enabledPath]);
        $process->run();

        // test nginx config
        $process = new Process(['sudo', 'nginx', '-t']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Nginx config test failed: ".$process->getErrorOutput());
        }

        // reload
        $process = new Process(['sudo', 'systemctl', 'reload', 'nginx']);
        $process->run();
    }

    public function enableSSL()
    {
        $process = new Process(['sudo', 'nginx', '-y']);
        $process->run();

        $process = new Process([
            'sudo',
            'certbot',
            '--nginx',
            '--non-interactive',
            '--agree-tos',
            '-m', 'admin@'.$this->domain,
            '-d', $this->domain,
            '-d', 'www.'.$this->domain
        ]);

        $process->run();


    }
}
