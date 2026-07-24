@extends('layouts.platform')

@section('title', 'Manage Modules')
@section('header', 'Module Management')

@section('content')
<div class="space-y-6">
    <div class="bento-card hero-animate p-8">
        <h2 class="font-heading text-xl font-bold text-brand-indigo dark:text-brand-cream">Modules & Pricing</h2>
        <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender mt-1">Enable or disable modules and set registration prices in Zambian Kwacha (ZMW).</p>
    </div>

    <div class="grid gap-4">
        @foreach($modules as $slug => $mod)
            @php
                $record = $moduleRecords[$slug] ?? null;
                $enabled = $record?->is_enabled ?? false;
                $price = (float) ($record?->price_zmw ?? 0);
            @endphp
            <div class="bento-card stagger-item p-6" data-hover-lift>
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-2xl {{ $mod['color'] }} shrink-0"></div>
                        <div>
                            <h3 class="font-heading font-semibold text-brand-indigo dark:text-brand-cream">{{ $mod['name'] }}</h3>
                            <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender">{{ $mod['description'] }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if($canManage)
                            <form method="POST" action="{{ route('platform.modules.price', $slug) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label class="font-sans text-sm text-brand-indigo/70">Price (ZMW)</label>
                                <input type="number" name="price_zmw" step="0.01" min="0" value="{{ number_format($price, 2, '.', '') }}"
                                    class="w-28 rounded-2xl border-brand-lavender/40 font-sans text-sm">
                                <button type="submit" class="px-4 py-2 rounded-full text-sm font-sans bg-brand-indigo text-white hover:opacity-90 transition">Save</button>
                            </form>
                            <form method="POST" action="{{ route('platform.modules.toggle', $slug) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-5 py-2 rounded-full text-sm font-sans font-medium transition {{ $enabled ? 'bg-brand-amber text-brand-indigo' : 'bg-brand-lavender/40 text-brand-indigo/70' }}">
                                    {{ $enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                            </form>
                        @else
                            <span class="text-sm font-sans text-brand-indigo/70">K {{ number_format($price, 2) }}</span>
                            <span class="text-sm font-sans {{ $enabled ? 'text-brand-amber' : 'text-brand-indigo/50' }}">{{ $enabled ? 'Enabled' : 'Disabled' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
