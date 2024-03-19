<?php

namespace App\Console\Commands;

use App\Jobs\TorInstanceJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
class tornodetest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tor:node:test';

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
        for ($x = 0; $x <= 100; $x++) {
            try {
                $q = Http::withOptions([
                    'proxy' => 'socks5://192.168.1.44:90'.sprintf("%02d",$x)
                ])->get("https://www.google.com.tr/search?q=berk&hl=tr");

                if (!$q->successful()) {
                    try {
                        $ssh = new SSH2("192.168.1.44", 22, 60);
                        if (!$ssh->login("root", '32334466')) {
                            $this->line($x." Bağlantı Hatası 1 !");
                        }
                        $ssh->setTimeout(0);
                        $this->line($x." Başarısız !");
                    } catch (\Exception $exception) {
                        $this->line($x." Bağlantı Hatası 2 !");
                    }

                } else {
                    $this->line($x." OK");
                }
            } catch (\Exception $exception){
                $this->line($x." Bağlantı Hatası 3 ! ".$exception->getMessage());
            }
        }
    }
}
