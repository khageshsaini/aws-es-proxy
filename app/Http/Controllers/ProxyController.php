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

    	//We don't want the control to be passed to response factory. There are issues in content type header.
    	//We will just die the script here
        die($proxy->run($request, $endpoint, $path, compact('credentials')));
    }
}
