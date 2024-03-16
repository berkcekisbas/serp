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
    {        try {
            $client = new \GuzzleHttp\Client();
            $request = $client->get('http://78.47.162.61/post',[
                'form_Data' => ['q' => urlencode("q=site:berk.com")]
            ]);
            $response = $request->getBody();
            echo $response;
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
