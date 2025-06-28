<?php
return [
    'dev_server' => [
        'enabled' => env('VITE_DEV_SERVER', false),
        'url' => 'http://localhost:5173',
        'check_certificate' => false,
    ],
    'build_directory' => 'build',
    'asset_url' => null,
    'ssr' => [
        'enabled' => false,
        'entry' => 'resources/js/ssr.js',
    ],
];
