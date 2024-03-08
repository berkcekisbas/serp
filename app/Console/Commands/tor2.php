<?php

namespace App\Console\Commands;

use App\Jobs\TorInstanceJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class tor2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tor2';

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


            TorInstanceJob::dispatch(sprintf("%02d",$x));
//
//            try {
//                $q = Http::withOptions([
//                    'proxy' => 'socks5://' . env('PROXY_IP') . ':90' . sprintf("%02d", $x)
//                ])->get("https://www.google.com.tr/search?q=berk&hl=tr");
//
//            } catch (\Exception $exception) {
//                $ssh = new SSH2(env('PROXY_IP'), 22, 60);
//                if (!$ssh->login("root", env('PROXY_ROOT_PASSWORD'))) {
//                    $this->release(2);
//                }
//                $ssh->setTimeout(0);
//                $ex = $ssh->exec('systemctl restart tor@' . $x);
//                $this->deleteOfflineInstance($x);
//            }
//
//        if ($q->successful()) {
//
//            $this->addOnlineInstance($x);
//
//        } else {
//            try {
//                $ssh = new SSH2(env('PROXY_IP'), 22, 60);
//                if (!$ssh->login("root", env('PROXY_ROOT_PASSWORD'))) {
//                }
//                $ssh->setTimeout(0);
//                $ex = $ssh->exec('systemctl restart tor@' . $x);
//
//            } catch (\Exception $exception) {
//
//            }
//            $this->deleteOfflineInstance($x);
//        }
        }
    }

    public function addOnlineInstance($name)
    {
        $instances = json_decode(Redis::get('instances'), true);
        $instances[$name] = $name;
        Redis::set('instances', json_encode($instances));
        $this->line("tor@" . $name . " Servis Başarılı");

    }

    public function deleteOfflineInstance($name)
    {
        $instances = json_decode(Redis::get('instances'), true);
        if (isset($instances[$name])) {
            unset($instances[$name]);
            Redis::set('instances', json_encode($instances));
        }
        $this->line("tor@" . $name . " Servis Başarısız");

    }
}
