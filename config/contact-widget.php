<?php

return [
    'user_model' => App\Models\User::class,

    'asset_version' => env('CONTACT_WIDGET_ASSET_VERSION', '1'),

    'routes' => [
        'prefix' => 'embed',
        'middleware' => ['web'],
    ],

    'legal' => [
        'privacy_url' => '/privacy',
        'terms_url' => '/terms',
    ],

    'form' => [
        'action' => '/call_me',
    ],

    'filament' => [
        'navigation_group' => 'Виджет связи',
    ],

    'embed' => [
        'script' => 'embed/contact-widget.js',
    ],
];
