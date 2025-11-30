<?php

return [
    'cuit' => env('AFIP_CUIT'),
    'production' => env('AFIP_PRODUCTION', false),
    'cert' => storage_path('afip/certs/cert.crt'),
    'key'  => storage_path('afip/certs/privada.key'),
    'passphrase' => env('AFIP_PASSPHRASE', null),
    'access_token' => env('AFIP_ACCESS_TOKEN', null),
];

