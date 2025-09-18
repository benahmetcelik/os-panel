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

        $domain = $this->domain;
        $email  = "admin@{$domain}";

        $process = new Process([
            'sudo', 'certbot',
            '--nginx',
            '--non-interactive',
            '--agree-tos',
            '-m', $email,
            '-d', $domain,
            '-d', "www.{$domain}"
        ]);

        $process->setTimeout(300);

        $process->mustRun();

        (new Process(['sudo', 'nginx', '-t']))->mustRun();
        (new Process(['sudo', 'systemctl', 'reload', 'nginx']))->mustRun();



    }
}
