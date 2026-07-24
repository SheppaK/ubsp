<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deployment panel (super-admin)
    |--------------------------------------------------------------------------
    |
    | Set DEPLOY_ENABLED=false in .env to hide the panel entirely on production.
    |
    */

    'enabled' => env('DEPLOY_ENABLED', true),

    'git' => [
        'enabled' => env('DEPLOY_GIT_ENABLED', true),
        'branch' => env('DEPLOY_GIT_BRANCH', 'main'),
        'remote' => env('DEPLOY_GIT_REMOTE', 'origin'),
    ],

    /*
    | Whitelisted artisan actions (key => command string).
    | Only these may be run from the admin deployment panel.
    */
    'artisan_commands' => [
        'migrate' => 'migrate --force',
        'config_clear' => 'config:clear',
        'config_cache' => 'config:cache',
        'route_clear' => 'route:clear',
        'route_cache' => 'route:cache',
        'view_clear' => 'view:clear',
        'view_cache' => 'view:cache',
        'cache_clear' => 'cache:clear',
        'optimize_clear' => 'optimize:clear',
        'optimize' => 'optimize',
    ],

    'artisan_labels' => [
        'migrate' => 'Run migrations',
        'config_clear' => 'Clear config cache',
        'config_cache' => 'Cache config',
        'route_clear' => 'Clear route cache',
        'route_cache' => 'Cache routes',
        'view_clear' => 'Clear compiled views',
        'view_cache' => 'Cache views',
        'cache_clear' => 'Clear application cache',
        'optimize_clear' => 'Clear all caches (optimize:clear)',
        'optimize' => 'Optimize (config + routes + views)',
    ],

];
