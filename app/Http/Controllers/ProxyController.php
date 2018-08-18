<?php

namespace App\Http\Controllers;

use Auth;
use App\Services\AwsEsProxyService;
use Psr\Http\Message\ServerRequestInterface;

class ProxyController extends Controller
{
    public function __invoke(AwsEsProxyService $proxy, ServerRequestInterface $request, $path)
    {	
    	$info = Auth::user();
    	$credentials = ['key' => $info->getAuthIdentifier(), 'secret' => $info->getAuthPassword()];
    	$endpoint = $info->endpoint;

        return $proxy->run($request, $endpoint, $path, compact('credentials'));
    }
}
