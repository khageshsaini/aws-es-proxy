<?php

namespace App\Services;
use GuzzleHttp\Psr7\Request;

class AwsServiceAccessAuthenticator
{
	protected $access_key;

	protected $access_secret;

	public function __construct($access_key, $access_secret)
	{
		$this->access_key = $access_key;
		$this->access_secret = $access_secret;
	}

	/**
	 * Checks if the access to the endpoint is valid or not
	 * @param  string  $endpoint     [description]
	 * @return boolean               [description]
	 * @throws [type]  [description]
	 */
	public function isAccessValid($endpoint)
	{
		$request = new Request('GET', $endpoint);

		//Now sign this request using params
		$params = ['credentials' => ['key' => $this->access_key, 'secret' => $this->access_secret]];
		$signer = new AwsRequestSigner($request);

		$client = new \GuzzleHttp\Client();

		try {
			$client->send($signer($params));
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
}