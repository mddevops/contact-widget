<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}').live,
            min: {{ $getMin() }},
            max: {{ $getMax() }},
            step: {{ $getStep() }},
            suffix: @js($getSuffix()),
        }"
        class="space-y-2"
    >
        <div class="flex items-center justify-between gap-3">
            <input
                type="range"
                x-model.number="state"
                :min="min"
                :max="max"
                :step="step"
                class="w-full accent-primary-600"
            >
            <span
                class="shrink-0 min-w-[2ch] text-sm font-medium text-gray-700 dark:text-gray-200"
                x-text="suffix !== '' ? (state ?? min) + suffix : String(state ?? min)"
            ></span>
        </div>
    </div>
</x-dynamic-component>
