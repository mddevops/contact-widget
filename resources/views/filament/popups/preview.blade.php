@php
    use SiteApps\ContactWidget\Enums\PopupListMarkerStyle;
    use SiteApps\ContactWidget\Models\SocialIcon;
    use SiteApps\ContactWidget\Support\Popup\PopupSettings;

    $markerIcons = collect(PopupListMarkerStyle::cases())
        ->mapWithKeys(fn (PopupListMarkerStyle $style) => [$style->value => $style->icon()])
        ->all();

    $buttonIconSvgs = SocialIcon::query()
        ->orderBy('title')
        ->pluck('svg', 'id')
        ->all();

    $settingDefaults = PopupSettings::defaults();
@endphp

<div
    class="popup-preview rounded-xl border border-gray-200 bg-gray-100 p-4 dark:border-gray-700 dark:bg-gray-900"
    wire:ignore.self
    x-data="{
        data: $wire.$entangle('data').live,
        markers: @js($markerIcons),
        icons: @js($buttonIconSvgs),
        settingDefaults: @js($settingDefaults),
        imageUrl: null,
        previewDevice: 'desktop',

        settings() {
            return { ...this.settingDefaults, ...(this.data?.settings ?? {}) };
        },

        benefitLines() {
            return (this.data?.benefits ?? '')
                .split(/\r\n|\r|\n/)
                .map(line => line.trim())
                .filter(Boolean);
        },

        markerIcon() {
            const key = this.settings().list_marker ?? 'check';
            return this.markers[key] ?? '✓';
        },

        layoutClass() {
            if (this.settings().desktop_hide_image) {
                return 'cbp-layout--desktop-no-image';
            }

            const position = this.settings().image_position ?? 'left';

            if (position === 'row-reverse' || position === 'left') {
                return 'cbp-layout--left';
            }

            if (position === 'row' || position === 'right') {
                return 'cbp-layout--right';
            }

            return 'cbp-layout--left';
        },

        mobileLayoutClass() {
            if (this.settings().mobile_hide_image) {
                return 'cbp-layout--mobile-no-image';
            }

            return (this.settings().mobile_image_position ?? 'top') === 'bottom'
                ? 'cbp-layout--mobile-bottom'
                : 'cbp-layout--mobile-top';
        },

        showDesktopImage() {
            return ! this.settings().desktop_hide_image;
        },

        showMobileImage() {
            return ! this.settings().mobile_hide_image;
        },

        imagePositionX() {
            return this.data?.settings?.image_x ?? this.settings().image_x ?? 'center';
        },

        imagePositionY() {
            return this.data?.settings?.image_y ?? this.settings().image_y ?? 'center';
        },

        imageScale() {
            const scale = parseInt(this.data?.settings?.image_scale ?? this.settings().image_scale ?? 100, 10);
            return Math.max(50, Math.min(500, scale));
        },
        
        mobileImageScale() {
            const scale = parseInt(this.data?.settings?.mobile_image_scale ?? this.settings().mobile_image_scale ?? 100, 10);
            return Math.max(50, Math.min(500, scale));
        },

        imageWidthPx() {
            const width = Number(this.data?.settings?.image_width ?? this.settings().image_width ?? 480);

            return Math.max(200, Math.min(720, Number.isFinite(width) ? width : 480));
        },

        contentWidthPx() {
            if (this.previewDevice === 'mobile') {
                const width = Number(this.data?.settings?.mobile_content_width ?? this.settings().mobile_content_width ?? 360);

                return Math.max(260, Math.min(480, Number.isFinite(width) ? width : 360));
            }

            const width = Number(this.data?.settings?.content_width ?? this.settings().content_width ?? 480);

            return Math.max(280, Math.min(720, Number.isFinite(width) ? width : 480));
        },

        mobileImageWidthPx() {
            return this.contentWidthPx();
        },

        modalWidthPx() {
            if (this.previewDevice === 'mobile') {
                return this.contentWidthPx();
            }

            let width = this.contentWidthPx();

            if (! this.settings().desktop_hide_image) {
                width += this.imageWidthPx();
            }

            return width;
        },

        mobileImageHeight() {
            const height = Number(this.data?.settings?.mobile_image_height_px ?? this.settings().mobile_image_height_px ?? 300);
            return Math.max(50, Math.min(500, Number.isFinite(height) ? height : 300));
        },

        borderRadius() {
            return parseInt(this.settings().border_radius ?? 16, 10);
        },

        desktopMediaStyle() {
            return {
                width: `${this.imageWidthPx()}px`,
                flex: `0 0 ${this.imageWidthPx()}px`,
                alignSelf: 'stretch',
                minHeight: '100%',
                backgroundColor: 'transparent',
                backgroundImage: this.imageUrl ? `url('${this.imageUrl}')` : 'none',
                backgroundSize: `${this.imageScale()}%`,
                backgroundPosition: `${this.imagePositionX()} ${this.imagePositionY()}`,
                backgroundRepeat: 'no-repeat',
            };
        },

        mobileMediaStyle() {
            const x = this.data?.settings?.mobile_image_x ?? this.settings().mobile_image_x ?? 'center';
            const y = this.data?.settings?.mobile_image_y ?? this.settings().mobile_image_y ?? 'center';
            const imageWidth = this.mobileImageWidthPx();

            return {
                width: `${imageWidth}px`,
                maxWidth: '100%',
                alignSelf: 'center',
                flex: `0 0 ${this.mobileImageHeight()}px`,
                height: `${this.mobileImageHeight()}px`,
                minHeight: '120px',
                backgroundColor: 'transparent',
                backgroundImage: this.imageUrl ? `url('${this.imageUrl}')` : 'none',
                backgroundSize: `${this.mobileImageScale()}%`,
                backgroundPosition: `${x} ${y}`,
                backgroundRepeat: 'no-repeat',
            };
        },

        contentBlockStyle() {
            const padding = this.previewDevice === 'mobile'
                ? parseInt(this.settings().mobile_content_padding ?? this.settings().content_padding ?? 20, 10)
                : parseInt(this.settings().content_padding ?? 20, 10);
            const background = this.settings().content_background ?? '#ffffff';
            const contentWidth = this.contentWidthPx();

            return `width: ${contentWidth}px; max-width: 100%; flex: 0 0 ${contentWidth}px; align-self: center; min-width: 0; padding: ${padding}px; background: ${background}; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box;`;
        },

        activeTitleSize() {
            const key = this.previewDevice === 'mobile' ? 'mobile_title_size' : 'title_size';

            return parseInt(this.settings()[key] ?? this.settings().title_size ?? 32, 10);
        },

        activeSubtitleSize() {
            const key = this.previewDevice === 'mobile' ? 'mobile_subtitle_size' : 'subtitle_size';

            return parseInt(this.settings()[key] ?? this.settings().subtitle_size ?? 16, 10);
        },

        activeBenefitsSize() {
            const key = this.previewDevice === 'mobile' ? 'mobile_benefits_size' : 'benefits_size';

            return parseInt(this.settings()[key] ?? this.settings().benefits_size ?? 14, 10);
        },

        buttonIconSvg() {
            const id = this.settings().button_icon_id;
            if (! id) {
                return '';
            }

            return this.icons[id] ?? '';
        },

        buttonIconSize() {
            return parseInt(this.settings().button_icon_size ?? 18, 10);
        },

        buttonIconColor() {
            return this.settings().button_icon_color ?? '#ffffff';
        },
    }"
    x-init="previewDevice = data?.preview_device ?? 'desktop'"
    x-effect="
        if (data?.preview_device) {
            previewDevice = data.preview_device;
        }
    "
    x-effect="
        const image = data?.image;
        if (! image) {
            imageUrl = null;
            return;
        }
        if (typeof image === 'string' && image !== '') {
            imageUrl = '/storage/' + image.replace(/^\\/?storage\\//, '');
            return;
        }
        if (Array.isArray(image) && image.length) {
            $wire.call('getPopupPreviewImageUrl').then(url => { if (url) imageUrl = url; });
        }
    "
>
    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 px-2">
        <div class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            Предварительный просмотр
        </div>

        <div class="inline-flex overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-800">
            <button
                type="button"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition"
                :class="previewDevice === 'desktop'
                    ? 'bg-primary-600 text-white'
                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'"
                @click="previewDevice = 'desktop'; data.preview_device = 'desktop'"
                title="Десктоп"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                </svg>
                Десктоп
            </button>
            <button
                type="button"
                class="inline-flex items-center gap-1.5 border-l border-gray-300 px-3 py-1.5 text-xs font-medium transition dark:border-gray-600"
                :class="previewDevice === 'mobile'
                    ? 'bg-primary-600 text-white'
                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'"
                @click="previewDevice = 'mobile'; data.preview_device = 'mobile'"
                title="Мобилка"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                Мобилка
            </button>
        </div>
    </div>

    <div class="mt-1 flex min-h-[28rem] items-center justify-center rounded-lg bg-gray-800/90 p-2">
        <div
            class="overflow-hidden bg-transparent shadow-2xl transition-all duration-300"
            :class="previewDevice === 'mobile' ? 'w-full' : 'w-full max-w-4xl'"
            :style="previewDevice === 'mobile'
                ? 'width: ' + modalWidthPx() + 'px; max-width: 100%; border-radius: ' + borderRadius() + 'px;'
                : 'width: ' + modalWidthPx() + 'px; max-width: 100%; border-radius: ' + borderRadius() + 'px;'"
        >
            {{-- Desktop --}}
            <div x-show="previewDevice === 'desktop'" class="cbp-layout" :class="layoutClass()" style="align-items: stretch;">
                <div
                    x-show="showDesktopImage() && imageUrl"
                    class="cbp-media"
                    :style="desktopMediaStyle()"
                    role="img"
                    aria-hidden="true"
                ></div>
                <div
                    x-show="showDesktopImage() && ! imageUrl"
                    class="cbp-media cbp-media--empty flex items-center justify-center text-sm text-gray-500"
                    :style="{
                        width: imageWidthPx() + 'px',
                        flex: '0 0 ' + imageWidthPx() + 'px',
                        alignSelf: 'stretch',
                        minHeight: '100%',
                    }"
                >
                    Изображение
                </div>

                @include('contact-widget::filament.popups.partials.preview-content')
            </div>

            {{-- Mobile --}}
            <div
                x-show="previewDevice === 'mobile'"
                class="cbp-layout cbp-layout--mobile"
                :class="mobileLayoutClass()"
                style="min-height: 480px;"
            >
                <div
                    x-show="showMobileImage() && imageUrl"
                    class="cbp-media cbp-media--mobile"
                    :style="mobileMediaStyle()"
                    role="img"
                    aria-hidden="true"
                ></div>
                <div
                    x-show="showMobileImage() && ! imageUrl"
                    class="cbp-media cbp-media--mobile cbp-media--empty flex items-center justify-center text-sm text-gray-500"
                    :style="{
                        width: mobileImageWidthPx() + 'px',
                        maxWidth: '100%',
                        alignSelf: 'center',
                        flex: '0 0 ' + mobileImageHeight() + 'px',
                        height: mobileImageHeight() + 'px',
                        minHeight: '120px',
                    }"
                >
                    Изображение
                </div>

                @include('contact-widget::filament.popups.partials.preview-content')
            </div>
        </div>
    </div>

    <style>
        .popup-preview .cbp-layout { display: flex; width: 100%; min-height: 320px; align-items: stretch; direction: ltr; }
        .popup-preview .cbp-media { background-color: transparent; align-self: stretch; }
        .popup-preview .cbp-media--empty {
            background-color: #ffffff;
            background-image: radial-gradient(circle, #c6dcff 1px, transparent 1px);
            background-size: 12px 12px;
        }
        .popup-preview .cbp-layout--left,
        .popup-preview .cbp-layout--right {
            flex-direction: row;
        }
        .popup-preview .cbp-layout--left .cbp-media { order: 0; }
        .popup-preview .cbp-layout--left .cbp-content { order: 1; }
        .popup-preview .cbp-layout--right .cbp-media { order: 1; }
        .popup-preview .cbp-layout--right .cbp-content { order: 0; }
        .popup-preview .cbp-layout--mobile { flex-direction: column; direction: ltr; }
        .popup-preview .cbp-layout--mobile-top { flex-direction: column; }
        .popup-preview .cbp-layout--mobile-bottom { flex-direction: column-reverse; }
        .popup-preview .cbp-layout--mobile-no-image { flex-direction: column; }
        .popup-preview .cbp-layout--desktop-no-image { flex-direction: column; }
        .popup-preview .cbp-title { margin: 0 0 0.75rem; line-height: 1.2; }
        .popup-preview .cbp-subtitle { margin: 0 0 1rem; line-height: 1.4; }
    </style>
</div>
