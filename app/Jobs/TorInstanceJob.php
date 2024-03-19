<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use phpseclib3\Net\SSH2;

class TorInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = PHP_INT_MAX;

    /**
     * Create a new job instance.
     */
    public function __construct(public $name)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $q = Http::withOptions([
                'proxy' => 'socks5://'.env('PROXY_IP').':90'.$this->name
            ])->get("https://www.google.com.tr/search?q=berk&hl=tr");

            if ($q->successful()) {
                $this->addOnlineInstance($this->name);
                Log::debug("Oooooley Tor Instance ".$this->name." Başarılı !");
                $this->release(60);
            } else {
                Log::debug("Başarısız Tor Instance ".$this->name." Tekrar Deneniyor !");
                try {
                    $ssh = new SSH2(env('PROXY_IP'), 22, 60);
                    if (!$ssh->login("root", env('PROXY_ROOT_PASSWORD'))) {
                        $this->release(2);
                    }
                    $ssh->setTimeout(0);
                } catch (\Exception $exception) {
                    $this->deleteOfflineInstance($this->name);
                    $this->release(2);
                }

                $ex = $ssh->exec('systemctl restart tor@'.$this->name);
                //$ex = $ssh->exec('systemctl start tor@'.$this->name);

                $this->deleteOfflineInstance($this->name);


                $this->release(2);
            }
        } catch (\Exception $exception){
            $instances = json_decode(Redis::get('instances'));
            $this->deleteOfflineInstance($this->name);
            $this->release(2);
        }
    }

    public function addOnlineInstance($name)
    {
        $instances = json_decode(Redis::get('instances'),true);
        $instances[$name] = $name;
        Redis::set('instances',json_encode($instances));
    }

    public function deleteOfflineInstance($name){
            $instances = json_decode(Redis::get('instances'),true);
            if (isset($instances[$name])) {
                unset($instances[$name]);
                Redis::set('instances',json_encode($instances));
            }
        }
}
