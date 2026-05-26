@php
    $languageLinks = collect(config('locales.supported'))->map(function ($label, $code) {
        return [
            'code' => $code,
            'label' => $label,
            'url' => request()->fullUrlWithQuery(['lang' => $code]),
        ];
    });
@endphp

<div x-data="{ open: false }" class="relative">
    <!-- Tombol Aktif -->
    <button type="button" @click="open = !open" @click.away="open = false" 
            class="flex items-center gap-1.5 rounded-full border border-white/20 bg-black/20 px-3 py-1.5 sm:px-4 sm:py-2 text-[0.7rem] sm:text-xs font-bold text-white backdrop-blur-md shadow-sm hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-white/30">
        <!-- Ikon Bahasa -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4 opacity-90">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
        </svg>
        <span>{{ strtoupper(app()->getLocale()) }}</span>
        <!-- Ikon Panah -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70 transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>
    
    <!-- Menu Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 mt-2.5 w-36 sm:w-40 rounded-2xl bg-white/95 backdrop-blur-xl p-1.5 shadow-2xl shadow-black/20 border border-white/50 z-[100]"
         style="display: none;">
        @foreach ($languageLinks as $language)
            <a href="{{ $language['url'] }}" 
               class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm transition-all focus:outline-none focus:bg-emerald-50 {{ app()->getLocale() === $language['code'] ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-neutral-700 font-medium hover:bg-neutral-100 hover:text-neutral-900' }}">
                <span class="w-6 text-center">{{ strtoupper($language['code']) }}</span>
                <span class="text-xs opacity-80">{{ $language['label'] }}</span>
                @if(app()->getLocale() === $language['code'])
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-600 ml-auto" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
