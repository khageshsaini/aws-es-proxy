<?php

namespace App\Services;

use GuzzleHttp;
use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Psr\Http\Message\RequestInterface;

class AwsEsProxyService
{
    /**
     * Runs The service.
     *
     * @return {[type]} [description]
     *
     * @throws [type] [description]
     */
    public function run($request, $endpoint, $path, array $params = [])
    {
        //We need to convert the original request and remove port
        $request = $this->getModifiedRequest($request);

        // Create a guzzle client
        $guzzle = new GuzzleHttp\Client();

        // Create the proxy instance
        $proxy = new Proxy(new GuzzleAdapter($guzzle));

        // Forward the request and get the response.
        $response = $proxy->forward($request)
                          ->filter(function ($request, $response, $next) use ($path) {
                              $request_uri = $request->getUri();
                              $request = $request->withUri(
                                  $request_uri->withPath($path)
                              );

                              // Call the next item in the middleware.
                              $response = $next($request, $response);

                              return $response;
                          })
                          ->filter(function ($request, $response, $next) use ($params, $endpoint) {
                              // Manipulate the request object.
                              $request = $this->getSignedRequest($request, $params);
                              // dd($request);

                              // Call the next item in the middleware.
                              $response = $next($request, $response);

                              return $response;
                          })
                          ->to($endpoint);

        // Output response to the browser.
        (new \Zend\Diactoros\Response\SapiEmitter())->emit($response);
    }

    /**
     * Removes Port.
     *
     * @param [type] $request [description]
     *
     * @return [type] [description]
     *
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
        $signer = new AwsRequestSigner($request);
        return $signer($params);
    }
}
