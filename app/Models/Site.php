<?php

namespace App\Models;

use App\Jobs\AddSLLToDomainJob;
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
    public function createSiteFolder()
    {
        $folder = $this->getSitePath();
        if (!File::exists($folder.'/public')) {
            File::makeDirectory($folder.'/public', 0755, true);
            File::put($folder.'/public/index.html', view('defaults.site_published',[
                'domain'=>$this->domain
            ]));
        }
    }

    /**
     * @return void
     */
    public function createNginxConfig()
    {

        $stub = file_get_contents(base_path('stubs/nginx/available.stub'));
        $config = str_replace(
            ['[[domain]]', '[[path]]'],
            [$this->domain, $this->getSitePath()],
            $stub
        );
        file_put_contents(storage_path('nginx/'.$this->domain.'.conf'), $config);

    }

    public function getSitePath()
    {
        return base_path('sites'.
            (!Str::startsWith($this->working_directory,'/') ? '/':'')
            .$this->working_directory);
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
        $process->start();
    }

    public function enableSSL()
    {
        dispatch(new AddSLLToDomainJob($this->domain))->delay(now()->addSecond())->onQueue('ssl');
    }
}
