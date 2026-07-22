@extends('layouts.platform')

@section('title', 'Manage Modules')
@section('header', 'Module Management')

@section('content')
<div class="space-y-6">
    <div class="bento-card hero-animate p-8">
        <h2 class="font-heading text-xl font-bold text-brand-indigo dark:text-brand-cream">Enable or Disable Modules</h2>
        <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender mt-1">Control which business systems are available on the platform.</p>
    </div>

    <div class="grid gap-4">
        @foreach($modules as $slug => $mod)
            @php $enabled = $enabledSlugs[$slug] ?? false; @endphp
            <div class="bento-card stagger-item flex items-center justify-between gap-4" data-hover-lift>
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl {{ $mod['color'] }} shrink-0"></div>
                    <div>
                        <h3 class="font-heading font-semibold text-brand-indigo dark:text-brand-cream">{{ $mod['name'] }}</h3>
                        <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender">{{ $mod['description'] }}</p>
                    </div>
                </div>
                @if($canManage)
                    <form method="POST" action="{{ route('platform.modules.toggle', $slug) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2 rounded-full text-sm font-sans font-medium transition {{ $enabled ? 'bg-brand-amber text-brand-indigo' : 'bg-brand-lavender/40 text-brand-indigo/70' }}">
                            {{ $enabled ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                @else
                    <span class="text-sm font-sans {{ $enabled ? 'text-brand-amber' : 'text-brand-indigo/50' }}">{{ $enabled ? 'Enabled' : 'Disabled' }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
