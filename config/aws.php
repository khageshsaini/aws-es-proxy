<?php

return [
    'service' => env('AWS_ES_SERVICE_NAME', 'es'),
    'region' => env('AWS_ES_REGION', 'us-east-1'),
    'endpoint' => env('AWS_ES_KIBANA_ENDPOINT'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY', 'access_key'),
        'secret' => env('AWS_ACCESS_SECRET', 'secret'),
    ],
];
