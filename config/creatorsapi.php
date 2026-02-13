<?php

return [
    'credentials' => [
        'id' => env('CREATORSAPI_CREDENTIAL_ID'),
        'secret' => env('CREATORSAPI_CREDENTIAL_SECRET'),
        'version' => env('CREATORSAPI_CREDENTIAL_VERSION'),
    ],
    'auth' => [
        'endpoint' => env('CREATORSAPI_AUTH_ENDPOINT'),
    ],
    'client' => [
        'host' => env('CREATORSAPI_HOST', 'https://creatorsapi.amazon'),
        'user_agent' => env('CREATORSAPI_USER_AGENT', 'creatorsapi-laravel'),
        'debug' => env('CREATORSAPI_DEBUG', false),
        'debug_file' => env('CREATORSAPI_DEBUG_FILE'),
        'temp_folder_path' => env('CREATORSAPI_TEMP_FOLDER'),
    ],
    'http' => [
        'timeout' => env('CREATORSAPI_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('CREATORSAPI_HTTP_CONNECT_TIMEOUT', 10),
        'proxy' => env('CREATORSAPI_HTTP_PROXY'),
        'verify' => env('CREATORSAPI_HTTP_VERIFY', true),
    ],
];
