<?php

namespace App\Http\Controllers;

use App\Jobs\TorInstanceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use phpseclib3\Net\SSH2;

class SerpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($q)
    {
        if (is_array(json_decode(Redis::get('instances'),true))){
            foreach (json_decode(Redis::get('instances')) as $instance){
                try {
                    $q = Http::withOptions([
                        'proxy' => 'socks5://'.env('PROXY_IP').':90'.$instance,
                    ])->get("https://www.google.com.tr/search?q=".$q."&hl=tr");
                    $q->body();
                }catch (\Exception $exception){
                    echo $exception->getMessage();
                }

                if ($q->successful()) {
                    return response()->json([
                        'status' => true,
                        'message' => "Sonuç alındı",
                        'data' => mb_convert_encoding($q->body(), 'UTF-8', 'UTF-8')
                    ],404);
                }else {
                    $instances = json_decode(Redis::get('instances'),true);
                    if (isset($instances[$instance])) {
                        unset($instances[$instance]);
                        Redis::set('instances',json_encode($instances));
                    }
                }
            }

            return response()->json([
                'status' => false,
                'message' => "Hiçbir instance dan cevap alınamadı",
                'data' => null
            ],404);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Aktif instance bulunamadı",
                'data' => null
            ],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function online()
    {
        if (is_array(json_decode(Redis::get('instances'),true))) {
            foreach (json_decode(Redis::get('instances')) as $instance) {
                echo "<p>".$instance."</p>";
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
