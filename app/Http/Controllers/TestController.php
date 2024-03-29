<?php

namespace App\Http\Controllers;

use App\Jobs\TorInstanceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use phpseclib3\Net\SSH2;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function test(Request $request)
    {
        echo phpinfo();
        exit;
        try {
            $client = new \GuzzleHttp\Client();
            $request = $client->get('http://78.47.162.61',[
                'query' => ['q' => urlencode("site:berk.com")]
            ]);
            $response = json_decode($request->getBody(),true);
            echo htmlspecialchars($response['data']);
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function test2(Request $request)
    {
        return response()->json([
            'status' => false,
            'message' => "Query string gerekli",
            'data' => $request->all()
        ],422);
    }
}
