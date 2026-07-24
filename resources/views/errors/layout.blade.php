<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteBrand->shortName() }} — @yield('code') @yield('title')</title>
    <x-brand-fonts />
    @include('partials.site-head')
    @vite(['resources/css/app.css'])
    @include('partials.theme-variables')
</head>
<body class="surface-page min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full text-center space-y-6">
        <a href="{{ route('home') }}" class="inline-flex mb-4">
            <x-site-logo />
        </a>

        <div class="bento-card p-10 space-y-4">
            <p class="font-heading text-6xl font-bold text-brand-coral">@yield('code')</p>
            <h1 class="font-heading text-2xl font-bold text-heading">@yield('title')</h1>
            <p class="font-sans text-muted">@yield('message')</p>

            <div class="flex flex-wrap justify-center gap-3 pt-4">
                @yield('actions')
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn-ghost text-sm">Go Back</a>
                <a href="{{ route('home') }}" class="btn-primary text-sm">Home</a>
            </div>
        </div>
    </div>
</body>
</html>
