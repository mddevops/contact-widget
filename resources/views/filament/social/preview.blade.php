@php
    use SiteApps\ContactWidget\Support\Social\SocialWidgetMobileSettings;

    $icons = $icons ?? [];
    $buttons = $buttons ?? [];
    $popups = $popups ?? [];
    $phoneIconId = \\SiteApps\\ContactWidget\\Models\\SocialIcon::query()->where('slug', 'phone')->value('id');
    $mobileDefaults = SocialWidgetMobileSettings::defaults();
@endphp

<div
    class="social-widget-preview rounded-xl border border-gray-200 bg-gray-100 p-4 dark:border-gray-700 dark:bg-gray-900"
    wire:ignore.self
    x-data="{
        data: $wire.$entangle('data').live,
        icons: @js($icons),
        fallbackButtons: @js($buttons),
        popupNames: @js($popups),
        phoneIconId: @js($phoneIconId),
        mobileDefaults: @js($mobileDefaults),
        previewDevice: 'desktop',
        open: false,
        popupOpen: false,
        popupData: { title: '', content: '' },

        settings() {
            return this.data ?? {};
        },

        mobileSettings() {
            return { ...this.mobileDefaults, ...(this.settings().mobile_settings ?? {}) };
        },

        activeSettings() {
            return this.previewDevice === 'mobile'
                ? this.mobileSettings()
                : this.settings();
        },

        iconSvg(id) {
            const resolvedId = id || this.phoneIconId;
            if (! resolvedId) return '';
            const key = String(resolvedId);
            return this.icons[resolvedId]?.svg ?? this.icons[key]?.svg ?? '';
        },

        positionClass() {
            const position = this.activeSettings().position ?? 'right';

            if (position === 'center') return 'social-widget--center';
            if (position === 'right') return 'social-widget--right';

            return 'social-widget--left';
        },

        animationClass() {
            const animation = this.settings().animation ?? 'pulse';

            return animation === 'none' ? '' : 'social-widget__main--' + animation;
        },

        displayModeClass() {
            const settings = this.activeSettings();
            const showLabels = Boolean(settings.show_labels) && ! Boolean(settings.tooltip_enabled);

            return showLabels ? 'social-widget--labeled' : 'social-widget--icons-only';
        },

        cssVars() {
            const s = this.activeSettings();
            const root = this.settings();
            const isMobile = this.previewDevice === 'mobile';
            const mainSize = Math.max(35, parseInt(s.main_button_size ?? 35, 10) || 35);
            const mainIconSize = Math.max(12, parseInt(s.main_icon_size ?? 18, 10) || 18);
            const itemIconSize = Math.max(12, parseInt(s.item_icon_size ?? 18, 10) || 18);
            const itemFontSize = Math.max(12, Math.min(32, parseInt(s.item_font_size ?? 13, 10) || 13));
            const offsetBottom = Math.max(0, parseInt(s.offset_bottom ?? 20, 10) || 0);
            const offsetSide = Math.max(0, parseInt(s.offset_side ?? 20, 10) || 0);
            const panelColor = isMobile
                ? (s.panel_background ?? root.popup_background ?? '#ffffff')
                : (root.popup_background ?? '#ffffff');
            const panelOpacity = Math.max(0, Math.min(100, parseInt(
                isMobile ? (s.panel_background_opacity ?? 100) : (root.panel_background_opacity ?? 100),
                10,
            ) || 0));
            const panelRadius = Math.max(0, Math.min(40, parseInt(
                isMobile ? (s.panel_border_radius ?? root.popup_border_radius ?? 6) : (root.popup_border_radius ?? 6),
                10,
            ) || 0));

            return `
                --widget-main-color: ${s.main_button_color ?? '#8e36ff'};
                --widget-text-color: ${s.main_button_text_color ?? '#ffffff'};
                --widget-panel-color: ${panelColor};
                --widget-panel-opacity: ${panelOpacity}%;
                --widget-radius: ${panelRadius}px;
                --widget-popup-shadow: ${root.popup_shadow ?? '0 5px 10px rgba(0, 0, 0, 0.1)'};
                --widget-panel-width: ${parseInt(root.popup_width ?? 260, 10)}px;
                --widget-main-size: ${mainSize}px;
                --widget-main-icon-size: ${mainIconSize}px;
                --widget-item-icon-size: ${itemIconSize}px;
                --widget-item-font-size: ${itemFontSize}px;
                --widget-offset-bottom: ${offsetBottom}px;
                --widget-offset-side: ${offsetSide}px;
            `;
        },

        normalizeButtons(raw) {
            if (! raw) {
                return this.fallbackButtons ?? [];
            }

            const list = Array.isArray(raw) ? raw : Object.values(raw);

            return list.length ? list : (this.fallbackButtons ?? []);
        },

        visibleButtons() {
            return this.normalizeButtons(this.data?.buttons)
                .filter(button => button && button.enabled !== false && button.enabled !== 0 && button.enabled !== '0')
                .sort((a, b) => (parseInt(a.sort ?? 0, 10)) - (parseInt(b.sort ?? 0, 10)));
        },

        showTooltips() {
            const settings = this.activeSettings();

            return Boolean(settings.tooltip_enabled) && ! Boolean(settings.show_labels);
        },

        showLabels() {
            return Boolean(this.activeSettings().show_labels);
        },

        handleButtonClick(button) {
            if (! button) return;

            this.open = false;

            if (button.open_type === 'popup' && button.popup_id) {
                this.popupData = {
                    title: this.popupNames[button.popup_id] ?? 'Попап',
                    content: 'На сайте откроется выбранный попап.',
                };
                this.popupOpen = true;
                return;
            }

            if (button.open_type === 'phone' && button.phone) {
                window.location.href = 'tel:' + String(button.phone).replace(/\\D+/g, '');
                return;
            }

            if (button.open_type === 'url' && button.url) {
                window.open(button.url, '_blank', 'noopener');
            }
        },
    }"
    x-init="previewDevice = data?.preview_device ?? 'desktop'"
    x-effect="
        if (data?.preview_device) {
            previewDevice = data.preview_device;
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

    <style>
        @include('contact-widget::widget-styles')
    </style>

    <div class="mt-1 flex min-h-[28rem] items-center justify-center rounded-lg bg-gray-800/90 p-2">
        <div
            class="relative overflow-hidden bg-gradient-to-b from-sky-100 to-slate-50 shadow-2xl transition-all duration-300 dark:from-slate-800 dark:to-slate-900"
            :style="previewDevice === 'mobile'
                ? 'width: 100%; max-width: 400px; min-height: 520px; border-radius: 24px;'
                : 'width: 100%; max-width: 100%; min-height: 28rem; border-radius: 12px;'"
        >
            <div
                class="social-widget-preview-frame"
                :class="previewDevice === 'mobile' ? 'social-widget-preview-frame--mobile' : ''"
            >
                <div
                    class="container social-widget"
                    :class="[positionClass(), displayModeClass(), open ? 'is-open' : '']"
                    :style="cssVars()"
                >
                    <span
                        class="close-btn social-widget__main"
                        :class="[animationClass(), open ? 'open' : '']"
                        @click="open = !open"
                    >
                        <span class="social-widget__icon" x-html="iconSvg(settings().main_icon_id || phoneIconId)"></span>
                    </span>

                    <div class="media-icons">
                        <template x-for="(button, index) in visibleButtons()" :key="button.id ?? ('btn-' + index)">
                            <a
                                href="#"
                                @click.prevent="handleButtonClick(button)"
                                :class="showLabels() ? 'social-widget__btn--labeled' : ''"
                                :style="`background: ${button.background_color ?? '#8e36ff'}; color: ${button.text_color ?? '#ffffff'};`"
                            >
                                <span class="social-widget__icon" x-html="iconSvg(button.icon_id)"></span>
                                <span
                                    x-show="showLabels()"
                                    class="social-widget__label"
                                    x-text="button.title"
                                ></span>
                                <span
                                    x-show="showTooltips()"
                                    class="tooltip"
                                    :style="`color: ${button.background_color ?? '#8e36ff'}`"
                                    x-text="button.title"
                                ></span>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="social-widget-popup" x-show="popupOpen" x-transition.opacity style="display: none;">
                    <div class="social-widget-popup__backdrop" @click="popupOpen = false"></div>
                    <div class="social-widget-popup__dialog" :style="cssVars()" @click.outside="popupOpen = false">
                        <button type="button" class="social-widget-popup__close" @click="popupOpen = false">&times;</button>
                        <h3 class="social-widget-popup__title" x-text="popupData.title"></h3>
                        <div class="social-widget-popup__content" x-text="popupData.content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
