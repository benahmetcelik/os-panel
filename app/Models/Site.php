<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        $nginx_config = file_get_contents( base_path('stubs/nginx/available.stub'));
        $edit_domain = str_replace('[[domain]]', $this->domain, $nginx_config);
        $edit_path = str_replace('[[path]]', $this->working_directory, $edit_domain);
        file_put_contents(nginxAvailableConfigPath(), $edit_path);

        $nginx_config = file_get_contents( base_path('stubs/nginx/enabled.stub'));
        $edit_domain = str_replace('[[domain]]', $this->domain, $nginx_config);
        $edit_path = str_replace('[[path]]', $this->working_directory, $edit_domain);
        file_put_contents(nginxEnabledConfigPath(), $edit_path);

    }

    public function getSitePath()
    {
        return public_path($this->working_directory);
    }
}
