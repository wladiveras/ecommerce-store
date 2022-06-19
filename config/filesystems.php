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
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [
        'cdn_old' => [
            'permPublic' => 0755,
            'driver' => 'sftp',
            'host' => '51.79.41.204',
            'username' => 'root',
            'password' => 'ecommerce@padraocolor##',
            'root' => '/var/www/html/node/images/',
            'visibility' => 'public',
            'api_secret' => env("CDN_SECRET","MACACO_DA_BOLA_AZUL"),
            'api_url' => env("CDN_API_URL","http://cdn.otimize.me:82"),
            'api_namespace' => env("CDN_API_NAMESPACE","padraocolor"),
            'full_url' => env("CDN_API_URL","http://cdn.otimize.me:82")."/".env("CDN_API_NAMESPACE","padraocolor")
        ],

        'cdn_local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'api_url' => env("CDN_API_URL","https://img.criafacil.com.br"),
            'api_namespace' => env("CDN_API_NAMESPACE","padraocolor"),
            'full_url' => env("CDN_API_URL","https://img.criafacil.com.br")."/".env("CDN_API_NAMESPACE","padraocolor")
        ],
        
        'cdn' => [
            'permPublic' => 0755,
            'driver' => 'local',
            'root' => '/var/www/img/',
            'visibility' => 'public',
            'api_secret' => env("CDN_SECRET","MACACO_DA_BOLA_AZUL"),
            'api_url' => env("CDN_API_URL","https://img.criafacil.com.br"),
            'api_namespace' => env("CDN_API_NAMESPACE","padraocolor"),
            'full_url' => env("CDN_API_URL","https://img.criafacil.com.br")."/".env("CDN_API_NAMESPACE","padraocolor")
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],

];//   
