<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteBrand->shortName() }} — @yield('title', 'Account')</title>

        <x-brand-fonts />
        @include('partials.site-head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.theme-variables')
    </head>
    <body class="surface-page">
        <div class="float-blob w-56 h-56 bg-brand-lavender -top-10 -right-10" aria-hidden="true"></div>
        <div class="float-blob w-40 h-40 bg-brand-amber bottom-20 -left-10" aria-hidden="true"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 relative">
            <a href="{{ route('home') }}" class="hero-animate mb-6">
                <x-site-logo />
            </a>

            <div class="hero-animate w-full sm:max-w-lg surface-card p-8 sm:p-10">
                {{ $slot }}
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
