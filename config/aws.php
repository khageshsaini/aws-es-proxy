<?php

return [
    'service' => env('AWS_ES_SERVICE_NAME', 'es'),
    'region' => env('AWS_ES_REGION', 'us-east-1'),
    'redirect_path' => '/_plugin/kibana/',
    'endpoint' => env('AWS_ES_KIBANA_ENDPOINT'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY', 'access_key'),
        'secret' => env('AWS_ACCESS_SECRET', 'secret'),
    ],
    'endpoints' => [
    	'CMC-DEV' => env('AWS_ES_VMOCK_DEV'),
    	'CMC-STAGING' => env('AWS_ES_VMOCK_STAGING'),
    	'CMC-LIVE' => env('AWS_ES_VMOCK_LIVE')
    ]
];
