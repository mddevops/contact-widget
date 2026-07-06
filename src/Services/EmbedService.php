<?php

namespace SiteApps\ContactWidget\Services;

use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Support\Facades\Cache;

class EmbedService
{
    public const VERSION_CACHE_KEY = 'embed.asset_version';

    public static function assetVersion(): string
    {
        return (string) Cache::get(self::VERSION_CACHE_KEY, config('contact-widget.asset_version', '1'));
    }

    public static function bumpAssetVersion(): void
    {
        Cache::forever(self::VERSION_CACHE_KEY, (string) time());
    }

    /**
     * @return array<string, mixed>
     */
    public static function config(?string $path = null): array
    {
        $normalizedPath = PopupService::normalizePath($path ?? request()?->path());
        $versionQuery = '?v=' . urlencode(self::assetVersion());

        return [
            'path' => $normalizedPath,
            'assets' => [
                'popupCss' => asset('embed/popup.css' . $versionQuery),
                'widgetCss' => asset('embed/widget.css' . $versionQuery),
                'modalJs' => asset('js/modules/callback-popup.js'),
                'queueJs' => asset('embed/popup-queue.js' . $versionQuery),
            ],
            'popups' => [
                'renderBaseUrl' => url('/popups'),
                'queue' => PopupService::queuePayload($normalizedPath),
                'idleGate' => [
                    'idleAfterBlockMs' => (int) config('contact-widget.popup.idle_after_block_ms', 3000),
                    'checkIntervalMs' => (int) config('contact-widget.popup.busy_check_interval_ms', 400),
                    'busySelectors' => config('contact-widget.popup.busy_selectors', []),
                ],
                'scroll' => [
                    'settleMs' => (int) config('contact-widget.popup.scroll_settle_ms', 400),
                    'minTimeOnPageMs' => (int) config('contact-widget.popup.scroll_min_time_on_page_ms', 0),
                ],
            ],
            'widget' => [
                'enabled' => SocialWidgetService::active() !== null,
                'htmlUrl' => route('contact-widget.embed.widget'),
            ],
            'form' => [
                'action' => config('contact-widget.form.action', '/call_me'),
            ],
        ];
    }
}
