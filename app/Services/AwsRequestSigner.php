<?php

namespace App\Services;

use Psr\Http\Message\RequestInterface;
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;

class AwsRequestSigner
{	
	protected $request;

	public function __construct(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function __invoke(array $params = [])
	{
		//Remove Connection header as it create issues with signing
        $request = $this->request->withoutHeader('connection');

        $service = isset($params['service']) ? $params['service'] : config('aws.service');
        $region = isset($params['region']) ? $params['region'] : config('aws.region');
        $options = isset($params['options']) ? $params['options'] : config('aws.options', []);
        $credentials = isset($params['credentials']) ? $params['credentials'] : config('aws.credentials');

        $signer = new SignatureV4($service, $region, $options);
        $credentials = new Credentials($credentials['key'], $credentials['secret']);

        return $signer->signRequest($request, $credentials);
	}
}