<?php

namespace App\Console\Commands\TorInstances;

use App\Jobs\TorInstanceJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
class StartJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tor:start:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($x = 0; $x <= env('PROXY_INSTANCE_COUNT'); $x++) {
            TorInstanceJob::dispatch(sprintf("%02d", $x));
        }
    }
}
