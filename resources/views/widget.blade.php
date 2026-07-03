@php
    /** @var \\SiteApps\\ContactWidget\\Models\\SocialWidget $widget */
    use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;

    $mainIconSvg = $widget->mainIcon?->svg ?? '';
    $buttons = $widget->relationLoaded('buttons')
        ? $widget->buttons->where('enabled', true)
        : $widget->buttons()->where('enabled', true)->orderBy('sort')->with(['icon', 'popup'])->get();
    $mobile = $widget->mergedMobileSettings();
    $desktopShowTooltips = $widget->tooltip_enabled && ! $widget->show_labels;
    $desktopShowLabels = $widget->show_labels && ! $widget->tooltip_enabled;
    $mobileShowTooltips = ($mobile['tooltip_enabled'] ?? true) && ! ($mobile['show_labels'] ?? false);
    $mobileShowLabels = ($mobile['show_labels'] ?? false) && ! ($mobile['tooltip_enabled'] ?? true);
    $desktopDisplayModeClass = SocialWidgetMobileSettings::displayModeClass($desktopShowLabels);
    $mobileDisplayModeClass = $mobileShowLabels ? 'social-widget--mobile-labeled' : 'social-widget--mobile-icons-only';
    $mobileRootClass = $mobileShowLabels ? 'social-widget-root--mobile-labeled' : 'social-widget-root--mobile-icons-only';
@endphp

<div
    class="social-widget-root {{ $mobileRootClass }}"
    data-desktop-position="{{ $widget->position }}"
    data-mobile-position="{{ $mobile['position'] ?? 'right' }}"
    @if($widget->desktop_only) data-desktop-only @endif
    @if($widget->mobile_only) data-mobile-only @endif
    style="
        --d-widget-main-color: {{ $widget->main_button_color }};
        --d-widget-text-color: {{ $widget->main_button_text_color }};
        --d-widget-main-size: {{ max(35, (int) $widget->main_button_size) }}px;
        --d-widget-main-icon-size: {{ max(12, (int) $widget->main_icon_size) }}px;
        --d-widget-offset-bottom: {{ max(0, (int) $widget->offset_bottom) }}px;
        --d-widget-offset-side: {{ max(0, (int) $widget->offset_side) }}px;
        --d-widget-item-icon-size: {{ max(12, (int) ($widget->item_icon_size ?? 18)) }}px;
        --d-widget-item-font-size: {{ max(12, min(32, (int) ($widget->item_font_size ?? 13))) }}px;
        --d-widget-panel-color: {{ $widget->popup_background }};
        --d-widget-panel-opacity: {{ max(0, min(100, (int) ($widget->panel_background_opacity ?? 100))) }}%;
        --m-widget-main-color: {{ $mobile['main_button_color'] ?? '#8e36ff' }};
        --m-widget-text-color: {{ $mobile['main_button_text_color'] ?? '#ffffff' }};
        --m-widget-main-size: {{ max(35, (int) ($mobile['main_button_size'] ?? 35)) }}px;
        --m-widget-main-icon-size: {{ max(12, (int) ($mobile['main_icon_size'] ?? 18)) }}px;
        --m-widget-offset-bottom: {{ max(0, (int) ($mobile['offset_bottom'] ?? 20)) }}px;
        --m-widget-offset-side: {{ max(0, (int) ($mobile['offset_side'] ?? 20)) }}px;
        --m-widget-item-icon-size: {{ max(12, (int) ($mobile['item_icon_size'] ?? 18)) }}px;
        --m-widget-item-font-size: {{ max(12, min(32, (int) ($mobile['item_font_size'] ?? 13))) }}px;
        --m-widget-panel-color: {{ $mobile['panel_background'] ?? $widget->popup_background }};
        --m-widget-panel-opacity: {{ max(0, min(100, (int) ($mobile['panel_background_opacity'] ?? 100))) }}%;
        --d-widget-radius: {{ max(0, min(40, (int) $widget->popup_border_radius)) }}px;
        --m-widget-radius: {{ max(0, min(40, (int) ($mobile['panel_border_radius'] ?? $widget->popup_border_radius))) }}px;
        --widget-popup-shadow: {{ $widget->popup_shadow }};
        --widget-panel-width: {{ (int) $widget->popup_width }}px;
    "
>
    <div
        class="container social-widget social-widget--pos-desktop-{{ $widget->position }} social-widget--pos-mobile-{{ $mobile['position'] ?? 'right' }} {{ $desktopDisplayModeClass }} {{ $mobileDisplayModeClass }}"
    >
        <span
            class="close-btn social-widget__main {{ $widget->animationClass() }}"
            data-widget-toggle
            role="button"
            tabindex="0"
            aria-label="{{ $widget->title }}"
        >
            <span class="social-widget__icon">{!! $mainIconSvg !!}</span>
        </span>

        <div class="media-icons">
            @foreach($buttons as $button)
                @if(! ($button->enabled ?? true))
                    @continue
                @endif
                @php
                    $href = $button->resolvedHref();
                    $isPopup = $button->isPopup();
                    $labelClass = $desktopShowLabels ? 'social-widget__btn--labeled' : '';
                @endphp
                <a
                    href="{{ $isPopup ? '#' : ($href ?? '#') }}"
                    class="{{ $labelClass }}"
                    @if($isPopup)
                        data-popup-id="{{ (int) $button->popup_id }}"
                    @elseif($button->open_type === 'phone' && $href)
                        href="{{ $href }}"
                        data-widget-close
                    @elseif($button->open_type === 'url' && $href)
                        href="{{ $href }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        data-widget-close
                    @else
                        data-widget-close
                    @endif
                    style="background: {{ $button->background_color }}; color: {{ $button->text_color }};"
                    aria-label="{{ $button->title }}"
                >
                    <span class="social-widget__icon">{!! $button->icon?->svg ?? '' !!}</span>
                    @if($desktopShowLabels)
                        <span class="social-widget__label social-widget__label--desktop">{{ $button->title }}</span>
                    @endif
                    @if($mobileShowLabels)
                        <span class="social-widget__label social-widget__label--mobile">{{ $button->title }}</span>
                    @endif
                    @if($desktopShowTooltips || $mobileShowTooltips)
                        <span class="tooltip social-widget__tooltip--desktop" style="color: {{ $button->background_color }}">{{ $button->title }}</span>
                        <span class="tooltip social-widget__tooltip--mobile" style="color: {{ $button->background_color }}">{{ $button->title }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
