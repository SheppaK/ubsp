<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.theme === 'dark', sidebarOpen: true }" x-init="$watch('dark', val => { localStorage.theme = val ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', val) }); if (localStorage.theme === 'dark') document.documentElement.classList.add('dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('ubsp.short_name', 'UBSP') }} — @yield('title', 'Dashboard')</title>
    <x-brand-fonts />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-variables')
    @stack('head')
</head>
<body class="surface-page">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside data-animate="sidebar" :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed inset-y-0 left-0 z-40 surface-sidebar transition-all duration-300 flex flex-col">
            <div class="flex items-center gap-3 px-4 h-16 border-b border-white/10">
                <div class="w-10 h-10 rounded-2xl bg-brand-coral flex items-center justify-center text-white font-heading font-bold text-sm shrink-0">U</div>
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p class="font-heading font-bold text-sm truncate text-white">{{ config('ubsp.short_name') }}</p>
                    <p class="text-xs text-brand-lavender truncate font-sans">Business Platform</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <a href="{{ route('platform.dashboard') }}" class="sidebar-link {{ request()->routeIs('platform.dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                @isset($accessibleModules)
                    <p x-show="sidebarOpen" class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-brand-lavender font-sans">Modules</p>
                    @foreach($accessibleModules as $slug => $mod)
                        <a href="{{ route('modules.'.$slug.'.dashboard') }}" class="sidebar-link {{ request()->is('modules/'.$slug.'*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <span class="w-5 h-5 shrink-0 rounded-lg {{ $mod['color'] }}"></span>
                            <span x-show="sidebarOpen" class="truncate">{{ $mod['name'] }}</span>
                        </a>
                    @endforeach
                @endisset

                @if(auth()->user()->isBusinessOwner())
                    @php $ownedBusiness = auth()->user()->ownedBusiness; @endphp
                    @if($ownedBusiness && ! $ownedBusiness->hasPaid())
                        <a href="{{ route('platform.business.payment') }}" class="sidebar-link sidebar-link-active">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span x-show="sidebarOpen">Pay to Unlock</span>
                        </a>
                    @else
                        <a href="{{ route('platform.business.dashboard') }}" class="sidebar-link {{ request()->routeIs('platform.business.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="sidebarOpen">My Business</span>
                        </a>
                    @endif
                @endif

                @role('super-admin|administrator')
                    <a href="{{ route('platform.theme-settings') }}" class="sidebar-link {{ request()->routeIs('platform.theme-settings*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <span x-show="sidebarOpen">Theme Colors</span>
                    </a>
                    <a href="{{ route('platform.email-settings') }}" class="sidebar-link {{ request()->routeIs('platform.email-settings*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span x-show="sidebarOpen">Email Settings</span>
                    </a>
                    <a href="{{ route('platform.kcpay-settings') }}" class="sidebar-link {{ request()->routeIs('platform.kcpay-settings*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span x-show="sidebarOpen">KC Pay Settings</span>
                    </a>
                @endrole

                @can('platform.manage-modules')
                    <a href="{{ route('platform.modules') }}" class="sidebar-link {{ request()->routeIs('platform.modules') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="sidebarOpen">Manage Modules</span>
                    </a>
                @endcan
            </nav>

            <div class="p-3 border-t border-white/10">
                <a href="{{ route('profile.edit') }}" class="sidebar-link sidebar-link-inactive">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full shrink-0 ring-2 ring-brand-lavender" alt="">
                    @else
                        <div class="w-8 h-8 rounded-full bg-brand-amber flex items-center justify-center text-brand-indigo text-xs font-bold shrink-0">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    @endif
                    <span x-show="sidebarOpen" class="truncate">{{ auth()->user()->name }}</span>
                </a>
            </div>
        </aside>

        {{-- Main --}}
        <div data-animate="main" :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300">
            <header class="sticky top-0 z-30 surface-header">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-2xl hover:bg-brand-lavender/20 transition text-brand-indigo dark:text-brand-cream">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-heading font-semibold text-brand-indigo dark:text-brand-cream">@yield('header', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="dark = !dark" class="p-2 rounded-2xl hover:bg-brand-lavender/20 transition text-brand-indigo dark:text-brand-cream" title="Toggle theme">
                            <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-sans text-muted hover:text-brand-coral transition">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 alert-success hero-animate">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
