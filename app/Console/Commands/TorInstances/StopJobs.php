<?php

namespace App\Console\Commands\TorInstances;

use App\Jobs\TorInstanceJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
class StopJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tor:stop:jobs';

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
        try {
            $ssh = new SSH2(env('PROXY_IP'), 22, 60);
            if (!$ssh->login("root", env('PROXY_ROOT_PASSWORD'))) {
                $this->line("HATA SSH BAĞLANAMADI");
                return 1;
            }
            $ssh->setTimeout(0);
            //$this->line($ssh->exec("ps aux | grep websockify | awk '{print $2}' | xargs kill -9"));
        } catch (\Exception $exception) {
            $this->line($exception->getMessage());
            return 1;
        }

        for ($x = 0; $x <= env('PROXY_INSTANCE_COUNT'); $x++) {
            $ex = $ssh->exec('tor-instance-create '.sprintf("%02d",$x));
            // $x değişkeni, instance numarasını belirtir.
            //$x = 1; // Örnek olarak 1 numaralı instance kullanılmıştır.

            $command = "sudo sed -i 's/SocksPort auto/SocksPort 127.0.0.1:90" . sprintf("%02d", $x) . "/' /etc/tor/instances/" . sprintf("%02d", $x) . "/torrc";
            $command .= " && sudo echo 'ExitPolicy reject *:* >> /etc/tor/instances/" . sprintf("%02d", $x) . "/torrc";
            $ex = $ssh->exec($command);

            //$ex = $ssh->exec("sudo sed -i 's/SocksPort auto/SocksPort 127.0.0.1:90".sprintf("%02d",$x)."/' /etc/tor/instances/".sprintf("%02d",$x)."/torrc");
            $ex = $ssh->exec('systemctl start tor@'.sprintf("%02d",$x));
            $this->line($ex);
            //$this->line("tor@".$x." Servis Başlatıldı");

            // $ex = $ssh->exec('systemctl stop tor@'.sprintf("%02d",$x));
            //$this->line($ex);
        }
    }
}
