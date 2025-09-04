<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
