@php
    $languageLinks = collect(config('locales.supported'))->map(function ($label, $code) {
        return [
            'code' => $code,
            'label' => $label,
            'url' => request()->fullUrlWithQuery(['lang' => $code]),
        ];
    });
@endphp

<div class="flex items-center gap-1 rounded-full border border-white/25 bg-black/20 p-1 text-xs font-semibold text-white backdrop-blur">
    <span class="sr-only">{{ __('public.language') }}</span>
    @foreach ($languageLinks as $language)
        <a href="{{ $language['url'] }}" class="rounded-full px-2.5 py-1.5 transition {{ app()->getLocale() === $language['code'] ? 'bg-white text-neutral-950' : 'text-white/85 hover:bg-white/15 hover:text-white' }}">
            {{ strtoupper($language['code']) }}
        </a>
    @endforeach
</div>
