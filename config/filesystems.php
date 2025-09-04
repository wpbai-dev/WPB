<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
     */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "wasabi", "digitalocean", etc...
    |
     */

    'disks' => [

        'direct' => [
            'driver' => 'local',
            'root' => public_path('/'),
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'digitalocean' => [
            'driver' => 's3',
            'key' => env('DGL_SPACES_KEY_ID'),
            'secret' => env('DGL_SPACES_SECRET_KEY'),
            'region' => env('DGL_SPACES_DEFAULT_REGION'),
            'bucket' => env('DGL_SPACES_BUCKET'),
            'url' => env('DGL_SPACES_URL'),
            'endpoint' => env('DGL_SPACES_ENDPOINT'),
            'use_path_style_endpoint' => env('DGL_SPACES_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'idrive' => [
            'driver' => 's3',
            'key' => env('IDRIVEE2_ACCESS_KEY_ID'),
            'secret' => env('IDRIVEE2_SECRET_ACCESS_KEY'),
            'region' => env('IDRIVEE2_DEFAULT_REGION'),
            'bucket' => env('IDRIVEE2_BUCKET'),
            'url' => env('IDRIVEE2_URL'),
            'endpoint' => env('IDRIVEE2_ENDPOINT'),
            'use_path_style_endpoint' => env('IDRIVEE2_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'storj' => [
            'driver' => 's3',
            'key' => env('STORJ_ACCESS_KEY_ID'),
            'secret' => env('STORJ_SECRET_ACCESS_KEY'),
            'region' => env('STORJ_DEFAULT_REGION'),
            'bucket' => env('STORJ_BUCKET'),
            'url' => env('STORJ_URL'),
            'endpoint' => env('STORJ_ENDPOINT'),
            'use_path_style_endpoint' => env('STORJ_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'cloudflare' => [
            'driver' => 's3',
            'key' => env('CR2_ACCESS_KEY_ID'),
            'secret' => env('CR2_SECRET_ACCESS_KEY'),
            'region' => env('CR2_DEFAULT_REGION'),
            'bucket' => env('CR2_BUCKET'),
            'url' => env('CR2_URL'),
            'endpoint' => env('CR2_ENDPOINT'),
            'use_path_style_endpoint' => env('CR2_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'contabo' => [
            'driver' => 's3',
            'key' => env('CONTABO_ACCESS_KEY_ID'),
            'secret' => env('CONTABO_SECRET_ACCESS_KEY'),
            'region' => env('CONTABO_DEFAULT_REGION'),
            'bucket' => env('CONTABO_BUCKET'),
            'url' => env('CONTABO_URL'),
            'endpoint' => env('CONTABO_ENDPOINT'),
            'use_path_style_endpoint' => env('CONTABO_USE_PATH_STYLE_ENDPOINT', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
     */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
