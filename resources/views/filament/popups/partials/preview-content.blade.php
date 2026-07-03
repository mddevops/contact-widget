<div
    class="cbp-content relative box-border"
    :style="contentBlockStyle()"
>
    <template x-if="data?.title">
        <h2
            class="cbp-title font-bold text-gray-900"
            :style="'font-size: ' + activeTitleSize() + 'px;'"
            x-text="data.title"
        ></h2>
    </template>

    <template x-if="data?.subtitle">
        <p
            class="cbp-subtitle text-gray-600"
            :style="'font-size: ' + activeSubtitleSize() + 'px;'"
            x-text="data.subtitle"
        ></p>
    </template>

    <template x-if="benefitLines().length">
        <ul class="cbp-list mb-4 list-none space-y-2 p-0">
            <template x-for="(benefit, index) in benefitLines()" :key="index">
                <li
                    class="cbp-list__item flex items-start gap-2 text-gray-800"
                    :style="'font-size: ' + activeBenefitsSize() + 'px;'"
                >
                    <span
                        class="cbp-list__icon shrink-0 font-bold"
                        :style="'color: ' + (settings().list_marker_color ?? '#22c55e') + ';'"
                        x-text="markerIcon()"
                    ></span>
                    <span x-text="benefit"></span>
                </li>
            </template>
        </ul>
    </template>

    <div class="space-y-3">
        <input
            type="text"
            readonly
            class="w-full border border-gray-300 bg-white px-4 py-3 text-gray-500"
            :style="'border-radius: ' + borderRadius() + 'px;'"
            :placeholder="settings().name_placeholder"
        >
        <input
            type="tel"
            readonly
            class="w-full border border-gray-300 bg-white px-4 py-3 text-gray-500"
            :style="'border-radius: ' + borderRadius() + 'px;'"
            :placeholder="settings().phone_placeholder"
        >
        <label class="flex items-start gap-2 text-xs leading-snug text-gray-500">
            <input type="checkbox" class="mt-0.5 rounded border-gray-300" checked disabled>
            <span x-text="settings().consent_text"></span>
        </label>
        <button
            type="button"
            class="flex w-full items-center justify-center gap-2 px-4 py-3 font-semibold"
            :style="`
                border-radius: ${borderRadius()}px;
                background: ${settings().button_color ?? '#22c55e'};
                color: ${settings().button_text_color ?? '#ffffff'};
            `"
        >
            <span
                x-show="buttonIconSvg()"
                class="cbp-form__btn-icon inline-flex shrink-0 items-center justify-center [&_svg]:h-full [&_svg]:w-full"
                :style="`width: ${buttonIconSize()}px; height: ${buttonIconSize()}px; color: ${buttonIconColor()};`"
                x-html="buttonIconSvg()"
            ></span>
            <span x-text="data?.button_text || 'Отправить заявку'"></span>
        </button>
    </div>
</div>
