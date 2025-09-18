<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class AddSLLToDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $domain;
    /**
     * Create a new job instance.
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $process = new Process(['sudo', 'nginx', '-y']);
        $process->run();


        $process = new Process([
            'bash', '-c',
            'sudo certbot --nginx --non-interactive --agree-tos -m admin@'.$this->domain.' -d '.$this->domain.' -d www.'.$this->domain.' > /var/log/certbot-laravel.log 2>&1 &'
        ]);
        $process->run();



    }
}
