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
        $x = Http::get("http://78.47.162.61/post",['q' => urlencode("berk")]);
        dd($x->json());
    }
}
