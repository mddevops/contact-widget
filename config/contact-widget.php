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

    /*
    | When true, PopupPolicy checks Filament Shield permissions (view_any_popup, etc.).
    | Leave false on projects without Shield — all panel users can manage popups.
    */
    'authorize_with_shield' => env('CONTACT_WIDGET_AUTHORIZE_WITH_SHIELD', false),

    'embed' => [
        'script' => 'embed/contact-widget.js',
    ],
];
