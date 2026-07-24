@props([
    'showName' => true,
    'nameClass' => 'font-heading font-bold text-xl text-brand-indigo',
    'logoClass' => 'w-11 h-11 rounded-2xl object-cover shrink-0',
    'fallbackClass' => 'w-11 h-11 rounded-2xl bg-brand-coral flex items-center justify-center text-white font-heading font-bold text-lg shrink-0',
    'stackClass' => 'flex items-center gap-3',
])

@php($brand = app(\App\Services\SiteBrandingService::class))

<div {{ $attributes->merge(['class' => $stackClass]) }}>
    @if($brand->hasLogo())
        <img src="{{ $brand->logoUrl() }}" alt="{{ $brand->shortName() }}" class="{{ $logoClass }}">
    @else
        <div class="{{ $fallbackClass }}">{{ $brand->initial() }}</div>
    @endif

    @if($showName)
        <span class="{{ $nameClass }}">{{ $brand->shortName() }}</span>
    @endif
</div>
