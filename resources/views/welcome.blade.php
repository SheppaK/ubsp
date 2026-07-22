<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('ubsp.short_name') }} — Universal Business Systems Platform</title>
    <x-brand-fonts />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-variables')
</head>
<body class="surface-page overflow-x-hidden">
    {{-- Decorative blobs --}}
    <div class="float-blob w-64 h-64 bg-brand-lavender top-20 -left-20" aria-hidden="true"></div>
    <div class="float-blob w-48 h-48 bg-brand-amber top-1/3 right-10" aria-hidden="true"></div>
    <div class="float-blob w-32 h-32 bg-brand-coral bottom-32 left-1/4" aria-hidden="true"></div>

    <div class="relative min-h-screen flex flex-col">
        <header class="surface-header sticky top-0 z-50 hero-animate">
            <div class="max-w-7xl mx-auto px-6 h-18 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-brand-coral flex items-center justify-center text-white font-heading font-bold text-lg">U</div>
                    <span class="font-heading font-bold text-xl text-brand-indigo">{{ config('ubsp.short_name') }}</span>
                </a>
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('platform.dashboard') }}" class="btn-primary text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost text-sm py-2 px-5">Log in</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary text-sm">Get Started</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1">
            {{-- Hero --}}
            <section class="max-w-7xl mx-auto px-6 pt-16 pb-24 lg:pt-24">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <span class="hero-animate inline-block tag-accent font-sans">Enterprise SaaS Platform</span>
                        <h1 class="hero-animate text-5xl lg:text-6xl font-heading font-bold leading-[1.1] text-brand-indigo">
                            Grow your business with smart digital solutions.
                        </h1>
                        <p class="hero-animate text-lg font-sans text-brand-indigo/70 max-w-lg">
                            {{ config('ubsp.name') }} — one login, eleven powerful business systems. Clinic, marketplace, university social, sports league, and more.
                        </p>
                        <div class="hero-animate flex flex-wrap gap-4">
                            <a href="{{ route('register') }}" class="btn-primary">Start Free</a>
                            <a href="{{ route('login') }}" class="btn-secondary">Sign In</a>
                        </div>
                        <div class="hero-animate flex gap-8 pt-4">
                            <div><p class="text-3xl font-heading font-bold text-brand-indigo" data-count="11">0</p><p class="text-sm font-sans text-brand-indigo/60">Modules</p></div>
                            <div><p class="text-3xl font-heading font-bold text-brand-indigo" data-count="11">0</p><p class="text-sm font-sans text-brand-indigo/60">User Roles</p></div>
                            <div><p class="text-3xl font-heading font-bold text-brand-coral">1</p><p class="text-sm font-sans text-brand-indigo/60">Login</p></div>
                        </div>
                    </div>

                    {{-- Bento visual grid --}}
                    <div class="grid grid-cols-2 gap-4 relative">
                        <div class="bento-card-dark col-span-2 p-8 stagger-item" data-hover-lift>
                            <p class="text-brand-lavender text-sm font-sans mb-2">Platform Overview</p>
                            <p class="font-heading text-2xl font-bold">All systems. One dashboard.</p>
                            <div class="mt-6 flex gap-2">
                                @foreach(['#5b5f97','#b8b8d1','#ffc145','#ff6b6c'] as $c)
                                    <div class="w-8 h-8 rounded-xl" style="background-color: {{ $c }}"></div>
                                @endforeach
                            </div>
                        </div>
                        @foreach(array_slice(config('ubsp.modules'), 0, 4) as $slug => $mod)
                            <div class="bento-card stagger-item" data-hover-lift>
                                <div class="w-10 h-10 rounded-2xl {{ $mod['color'] }} mb-4"></div>
                                <h3 class="font-heading font-semibold text-brand-indigo">{{ $mod['name'] }}</h3>
                            </div>
                        @endforeach
                        <div class="bento-card-accent stagger-item" data-hover-lift>
                            <p class="font-heading font-bold text-lg">+7 more</p>
                            <p class="text-sm font-sans mt-1 opacity-80">modules ready to use</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Module bento grid --}}
            <section class="bg-brand-lavender/20 py-24">
                <div class="max-w-7xl mx-auto px-6">
                    <h2 class="stagger-item font-heading text-3xl font-bold text-brand-indigo text-center mb-4">Your Business Toolkit</h2>
                    <p class="stagger-item font-sans text-brand-indigo/70 text-center mb-12 max-w-2xl mx-auto">Every module works independently yet connects seamlessly — like Microsoft 365 for your organization.</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach(config('ubsp.modules') as $slug => $mod)
                            <div class="bento-card stagger-item" data-hover-lift>
                                <div class="w-11 h-11 rounded-2xl {{ $mod['color'] }} flex items-center justify-center mb-4">
                                    <span class="font-heading font-bold text-white text-sm">{{ substr($mod['name'], 0, 1) }}</span>
                                </div>
                                <h3 class="font-heading font-semibold text-brand-indigo">{{ $mod['name'] }}</h3>
                                <p class="text-sm font-sans text-brand-indigo/60 mt-2 line-clamp-2">{{ $mod['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- CTA --}}
            <section class="max-w-7xl mx-auto px-6 py-24">
                <div class="bento-card-dark text-center p-12 lg:p-16 stagger-item">
                    <h2 class="font-heading text-3xl lg:text-4xl font-bold mb-4">Ready to unify your business systems?</h2>
                    <p class="font-sans text-brand-lavender mb-8 max-w-xl mx-auto">Join UBSP today and give your team one place to work, collaborate, and grow.</p>
                    <a href="{{ route('register') }}" class="btn-primary inline-flex">Create Free Account</a>
                </div>
            </section>
        </main>

        <footer class="surface-header py-8 text-center">
            <p class="text-sm font-sans text-brand-indigo/60">&copy; {{ date('Y') }} {{ config('ubsp.short_name') }}. Built with Laravel & MySQL.</p>
        </footer>
    </div>
</body>
</html>
