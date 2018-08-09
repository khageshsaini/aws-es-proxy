<?php

namespace App\Http\Controllers;

use App\Services\AwsEsProxyService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Request;

class ProxyController extends Controller
{
    public function __invoke(AwsEsProxyService $proxy, ServerRequestInterface $request, string $path)
    {		
        return $proxy->run($request, config('aws.endpoint'), $path);
    }
}
