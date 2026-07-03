@php
    use SiteApps\ContactWidget\Models\Popup;
    use SiteApps\ContactWidget\Support\Popup\PopupSettings;

    /** @var Popup $popup */

    $settings = $popup->resolvedSettings();
    $radius = (int) ($settings['border_radius'] ?? 16);
    $imageScale = PopupSettings::imageScale($settings);
    $imageX = $settings['image_x'] ?? 'center';
    $imageY = $settings['image_y'] ?? 'center';
    $mobileImageHeightPx = PopupSettings::mobileImageHeightPx($settings);
    $mobileImageScale = PopupSettings::mobileImageScale($settings);
    $mobileImageX = $settings['mobile_image_x'] ?? 'center';
    $mobileImageY = $settings['mobile_image_y'] ?? 'center';
    $mediaVisibilityClasses = $popup->mediaVisibilityClasses();
    $buttonIconSvg = $popup->buttonIconSvg();
    $buttonIconSize = (int) ($settings['button_icon_size'] ?? 18);
    $buttonIconColor = $settings['button_icon_color'] ?? '#ffffff';
    $contentCssVars = PopupSettings::cssVariables($settings, $popup->hasImage());
    $contentCssVarsString = collect($contentCssVars)->map(fn (string $value, string $key): string => $key.': '.$value)->implode('; ');
@endphp

<div class="cbp-root" id="cbp-{{ $popup->id }}" style="padding: 0; border-radius: {{ $radius }}px; {{ $contentCssVarsString }}; --cbp-mobile-image-height-px: {{ $mobileImageHeightPx }}px; --cbp-mobile-image-scale: {{ $mobileImageScale }}%; --cbp-mobile-image-x: {{ $mobileImageX }}; --cbp-mobile-image-y: {{ $mobileImageY }};">
    <div class="cbp-box" style="border-radius: {{ $radius }}px;">
        <div class="cbp-layout {{ $popup->layoutClasses() }}">
            @if($popup->shouldRenderMedia())
                <div
                    class="cbp-media {{ $mediaVisibilityClasses }}"
                    style="
                        background-image: url('{{ asset('storage/'.$popup->image) }}');
                        background-size: {{ $imageScale }}%;
                        background-position: {{ $imageX }} {{ $imageY }};
                    "
                    role="img"
                    aria-label="{{ $popup->title }}"
                ></div>
            @endif

            <div class="cbp-content">
                <h2 class="cbp-title">
                    {{ $popup->title }}
                </h2>
                @if($popup->subtitle)
                    <p class="cbp-subtitle">
                        {{ $popup->subtitle }}
                    </p>
                @endif

                @if($popup->benefitLines() !== [])
                    <ul class="cbp-list">
                        @foreach($popup->benefitLines() as $benefit)
                            <li class="cbp-list__item">
                                <span class="cbp-list__icon">{{ $popup->listMarkerIcon() }}</span>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form class="cbp-form" id="callback">
                    <div class="cbp-form__fields">
                        <input
                            class="cbp-form__input"
                            type="text"
                            name="name"
                            placeholder="{{ $settings['name_placeholder'] }}"
                            style="border-radius: {{ $radius }}px;"
                        >
                        <input
                            class="cbp-form__input"
                            type="tel"
                            name="telephone"
                            placeholder="{{ $settings['phone_placeholder'] }}"
                            autocomplete="off"
                            required
                            style="border-radius: {{ $radius }}px;"
                        >
                    </div>
                    <input type="hidden" name="comment" value="Попап: {{ $popup->title }}">
                    <input type="hidden" name="form_name" value="help">
                    <button
                        type="submit"
                        class="cbp-form__btn"
                        style="
                            border-radius: {{ $radius }}px;
                            background: {{ $settings['button_color'] }};
                            color: {{ $settings['button_text_color'] }};
                        "
                    >
                        @if($buttonIconSvg)
                            <span class="cbp-form__btn-icon" style="width: {{ $buttonIconSize }}px; height: {{ $buttonIconSize }}px; color: {{ $buttonIconColor }};">
                                {!! $buttonIconSvg !!}
                            </span>
                        @endif
                        {{ $popup->button_text }}
                    </button>
                    <label class="cbp-form__note">
                        <input type="checkbox" required>
                        <span>
                                Подтверждаю что ознакомлен(а) с <a href="{{ url(config('contact-widget.legal.privacy_url', '/privacy')) }}">политикой конфиденциальности</a> и <a
                                    href="{{ url(config('contact-widget.legal.terms_url', '/terms')) }}">положением об обработке персональных данных</a> и даю согласие на обработку моих персональных
                                данных
                        </span>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>
