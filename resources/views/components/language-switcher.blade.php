@php
    // Check if multi-language is enabled and if there are multiple languages available
    $multiLanguageEnabled = \App\Models\Setting::get('localization.multi_language_enabled', true);
    $availableLocales = \App\Models\Setting::get('app.available_locales', ['it', 'en']);
    $availableLocales = is_array($availableLocales) ? $availableLocales : ['it', 'en'];
    $showLanguageSwitcher = $multiLanguageEnabled && count($availableLocales) > 1;

    // Language name mapping
    $languageNames = [
        'it' => 'Italian',
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'pt' => 'Portuguese',
        'nl' => 'Dutch',
        'ru' => 'Russian',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ar' => 'Arabic'
    ];

    $currentLocale = app()->getLocale();
    $getCurrentLanguageName = function($locale) use ($languageNames) {
        return $languageNames[$locale] ?? ucfirst($locale);
    };
@endphp

@if($showLanguageSwitcher)
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm">
        <div class="flex items-center">
            <span class="font-medium">
                {{ $getCurrentLanguageName($currentLocale) }}
            </span>
        </div>
        <div class="ml-2">
            <svg class="fill-current h-4 w-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </button>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="display: none;">
        <div class="py-2">
            @foreach($availableLocales as $locale)
                @php
                    $isSelected = $currentLocale === $locale;
                @endphp
                <form method="POST" action="{{ route('language.switch') }}" class="block">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $locale }}">
                    <button type="submit" class="w-full text-left px-4 py-3 text-sm hover:bg-blue-50 transition duration-150 flex items-center justify-between {{ $isSelected ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <div class="flex items-center">
                            <span class="font-medium">
                                {{ $getCurrentLanguageName($locale) }}
                            </span>
                        </div>
                        @if($isSelected)
                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endif