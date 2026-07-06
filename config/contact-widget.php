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

    /*
    | Auto-popup idle gate: wait while other modals (Bootstrap, Fancybox, etc.) are open.
    */
    'popup' => [
        'idle_after_block_ms' => (int) env('CONTACT_WIDGET_POPUP_IDLE_AFTER_BLOCK_MS', 3000),
        'busy_check_interval_ms' => (int) env('CONTACT_WIDGET_POPUP_BUSY_CHECK_INTERVAL_MS', 400),
        'busy_selectors' => [],
        'scroll_settle_ms' => (int) env('CONTACT_WIDGET_POPUP_SCROLL_SETTLE_MS', 400),
        'scroll_min_time_on_page_ms' => (int) env('CONTACT_WIDGET_POPUP_SCROLL_MIN_TIME_MS', 0),
    ],
];
