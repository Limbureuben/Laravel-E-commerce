<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'submit-payment'],

    'allowed_methods' => ['*'],

    // 'allowed_origins' => ['http://localhost:5173'],

    'allowed_origins' => ['http://localhost:5173'],
    'allowed_origins_patterns' => ['/^https:\/\/.*\.ngrok-free\.app$/'],


    // 'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
