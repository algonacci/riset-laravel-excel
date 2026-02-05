@php
    $currentLocale = app()->getLocale();
    $availableLocales = [
        'id' => ['flag' => '🇮🇩', 'name' => 'Bahasa Indonesia'],
        'en' => ['flag' => '🇺🇸', 'name' => 'English'],
    ];
@endphp

<div class="fi-topbar-item flex items-center me-4">
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button
                type="button"
                class="flex items-center gap-x-2 py-2 px-3 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 outline-none focus:ring-2 focus:ring-primary-500/50 h-9"
                title="Change Language"
            >
                <div class="flex items-center gap-x-2">
                    <span class="text-lg leading-none flex">{{ $availableLocales[$currentLocale]['flag'] }}</span>
                    <span class="font-bold tracking-tight">{{ strtoupper($currentLocale) }}</span>
                </div>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($availableLocales as $locale => $data)
                <x-filament::dropdown.list.item
                    tag="a"
                    :href="'?lang=' . $locale"
                    :color="$locale === $currentLocale ? 'primary' : 'gray'"
                >
                    <div class="flex items-center gap-3 min-w-[140px]">
                        <span class="text-lg leading-none">{{ $data['flag'] }}</span>
                        <span class="font-medium">{{ $data['name'] }}</span>
                    </div>
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
