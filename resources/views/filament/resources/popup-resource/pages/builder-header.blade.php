<header class="fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        @if ($breadcrumbs)
            <x-filament::breadcrumbs
                :breadcrumbs="$breadcrumbs"
                class="mb-2 hidden sm:block"
            />
        @endif

        <div class="flex flex-wrap items-center gap-4">
            <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $heading }}
            </h1>

            <label @class([
                'inline-flex cursor-pointer items-center gap-2',
                'hidden' => ! ($showActiveToggle ?? false),
            ])>
                <input
                    type="checkbox"
                    wire:model.live="data.is_active"
                    class="fi-checkbox-input rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-primary-600 focus:ring-offset-0 disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-gray-400 disabled:checked:text-gray-400 dark:bg-white/5 dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600 text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:text-primary-500 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50 dark:disabled:ring-white/10"
                >
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Активен</span>
            </label>
        </div>
    </div>

    <div @class(['flex shrink-0 items-center gap-3', 'sm:mt-7' => $breadcrumbs])>
        @if ($actions)
            <x-filament::actions :actions="$actions" />
        @endif
    </div>
</header>
