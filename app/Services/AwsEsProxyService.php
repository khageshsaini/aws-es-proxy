<?php

namespace App\Services;

use GuzzleHttp;
use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Uri;

class AwsEsProxyService
{
    /**
     * Runs The service.
     *
     * @return {[type]} [description]
     *
     * @throws [type] [description]
     */
    public function run($request, string $endpoint, array $params = [])
    {       

        //We need to convert the original request and remove port
        $request = $this->getModifiedRequest($request);

        // Create a guzzle client
        $guzzle = new GuzzleHttp\Client();

        // Create the proxy instance
        $proxy = new Proxy(new GuzzleAdapter($guzzle));

        // Forward the request and get the response.
        $response = $proxy->forward($request)
                          ->filter(function ($request, $response, $next) use ($params, $endpoint) {
                            // Manipulate the request object.
                            $request = $this->getSignedRequest($request, $params);

                            // Call the next item in the middleware.
                            $response = $next($request, $response);

                            return $response;
                          })
                          ->to($endpoint);

        // Output response to the browser.
        (new \Zend\Diactoros\Response\SapiEmitter())->emit($response);
    }

    /**
     * Removes Port 
     * @param  [type] $request      [description]
     * @return [type]               [description]
     * @throws [type] [description]
     */
    private function getModifiedRequest($request)
    {
        $request_uri = $request->getUri();
        return $request->withUri($request_uri->withPort(null));
    }

    /**
     * Signs the request.
     *
     * @param {[type]} RequestInterface $request [description]
     *
     * @return {[type]} [description]
     *
     * @throws [type] [description]
     */
    private function getSignedRequest(RequestInterface $request, array $params = [])
    {       
        //Remove Connection header as it create issues with signing
        $request = $request->withoutHeader('connection');

        $service = isset($params['service']) ? $params['service'] : config('aws.service');
        $region = isset($params['region']) ? $params['region'] : config('aws.region');
        $options = isset($params['options']) ? $params['options'] : config('aws.options', []);
        $credentials = isset($params['credentials']) ? $params['credentials'] : config('aws.credentials');

        $signer = new \Aws\Signature\SignatureV4($service, $region, $options);
        $credentials = new \Aws\Credentials\Credentials($credentials['key'], $credentials['secret']);

        return $signer->signRequest($request, $credentials);
    }
}
