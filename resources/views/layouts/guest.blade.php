<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('ubsp.short_name', 'UBSP') }} — @yield('title', 'Account')</title>

        <x-brand-fonts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.theme-variables')
    </head>
    <body class="surface-page">
        <div class="float-blob w-56 h-56 bg-brand-lavender -top-10 -right-10" aria-hidden="true"></div>
        <div class="float-blob w-40 h-40 bg-brand-amber bottom-20 -left-10" aria-hidden="true"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 relative">
            <a href="{{ route('home') }}" class="hero-animate mb-6 flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-brand-coral flex items-center justify-center text-white font-heading font-bold text-lg">U</div>
                <span class="font-heading font-bold text-xl text-brand-indigo">{{ config('ubsp.short_name') }}</span>
            </a>

            <div class="hero-animate w-full sm:max-w-md surface-card p-8 sm:p-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
