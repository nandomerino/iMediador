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
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'WPcss' => getcwd() . "/WP/wp-content/uploads/fusion-styles/",
            'WPjs' => getcwd() . "/WP/wp-content/uploads/fusion-scripts/",
            'downloads' => public_path() . '/downloads/',
            'sliders' => public_path() . '/sliders/',
            'certs' => [
                'demo' => [
                    'path' => app_path() . '/Http/Controllers/logaltyclient/certs/prevision_mallorquina_demo.pfx',
                    'pass' => 'logalty',
                    ],
                'prod' => [
                    'path' => app_path() . '/Http/Controllers/logaltyclient/certs/prevision_mallorquina.pfx',
                    'pass' => 'xxxxxxxxx',
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'home' => env('APP_URL'),
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'app' => env('APP_URL') . '/app',
            'WPcss' => "/WP/wp-content/uploads/fusion-styles/",
            'WPjs' => "/WP/wp-content/uploads/fusion-scripts/",
            'WPapi' => env('APP_URL') . '/WP/wp-json/wp/v2/',
            'urlCheck' => env('APP_URL') . '/WP/app',
            'visibility' => 'public',
            'downloads' => '/downloads/',
        ],

        'app' => [
            'driver' => 'local',
            'home' => env('APP_URL') . '/app',
            'visibility' => 'private',
        ],

        'panel' => [
            'driver' => 'local',
            'login' => env('APP_URL') . '/panel/login',
            'home' => env('APP_URL') . '/panel',
        ],

        'pmapi' => [
            'url' => env('PMAPI_URL'),
            'devmode' => env('DEVMODE'), // set blank to disable
        ],

    ],

];
