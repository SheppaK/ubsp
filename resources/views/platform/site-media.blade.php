@extends('layouts.platform')

@section('title', 'Site Media')
@section('header', 'Site Media & Branding')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="bento-card p-4 border-green-300 bg-green-50 font-sans text-green-800">{{ session('success') }}</div>
    @endif

    <p class="font-sans text-brand-indigo/70">Update the site name, logo, and favicon shown on the welcome page, login screens, and admin dashboard.</p>

    <form method="POST" action="{{ route('platform.site-media.update') }}" enctype="multipart/form-data" class="bento-card p-8 space-y-5 hero-animate">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="site_name" value="Site name (full)" />
            <x-text-input id="site_name" name="site_name" class="block mt-1 w-full" :value="old('site_name', $settings->site_name)" required />
            <p class="mt-1 text-xs text-brand-indigo/50">Used in page titles and marketing copy.</p>
            <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="site_short_name" value="Short name" />
            <x-text-input id="site_short_name" name="site_short_name" class="block mt-1 w-full" :value="old('site_short_name', $settings->site_short_name)" required maxlength="64" />
            <p class="mt-1 text-xs text-brand-indigo/50">Shown in the sidebar and header (e.g. UBSP).</p>
            <x-input-error :messages="$errors->get('site_short_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="tagline" value="Tagline (optional)" />
            <x-text-input id="tagline" name="tagline" class="block mt-1 w-full" :value="old('tagline', $settings->tagline)" />
            <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
            <div>
                <x-input-label for="logo" value="Logo" />
                @if($settings->logo_path)
                    <div class="mt-2 mb-3 p-3 rounded-xl bg-brand-indigo/5 inline-block">
                        <img src="{{ $settings->logoUrl() }}" alt="Current logo" class="h-16 w-auto max-w-full object-contain">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-brand-indigo/70">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-brand-lavender text-brand-indigo focus:ring-brand-indigo">
                        Remove current logo
                    </label>
                @endif
                <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp"
                       class="mt-2 block w-full text-sm text-brand-indigo/70 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand-indigo/10 file:text-brand-indigo hover:file:bg-brand-indigo/20">
                <p class="mt-1 text-xs text-brand-indigo/50">PNG, JPG, SVG, or WebP. Max 2 MB. Square or horizontal works best.</p>
                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="favicon" value="Favicon" />
                @if($settings->favicon_path)
                    <div class="mt-2 mb-3 p-3 rounded-xl bg-brand-indigo/5 inline-block">
                        <img src="{{ $settings->faviconUrl() }}" alt="Current favicon" class="h-8 w-8 object-contain">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-brand-indigo/70">
                        <input type="checkbox" name="remove_favicon" value="1" class="rounded border-brand-lavender text-brand-indigo focus:ring-brand-indigo">
                        Remove current favicon
                    </label>
                @endif
                <input id="favicon" name="favicon" type="file" accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/svg+xml,image/jpeg,image/webp,.ico"
                       class="mt-2 block w-full text-sm text-brand-indigo/70 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand-indigo/10 file:text-brand-indigo hover:file:bg-brand-indigo/20">
                <p class="mt-1 text-xs text-brand-indigo/50">ICO or PNG, 32×32 or 64×64 recommended. Max 512 KB.</p>
                <x-input-error :messages="$errors->get('favicon')" class="mt-2" />
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-primary">Save branding</button>
        </div>
    </form>

    <form method="POST" action="{{ route('platform.site-media.reset') }}" onsubmit="return confirm('Reset to default site name and remove uploaded media?');">
        @csrf
        <button type="submit" class="btn-secondary text-sm">Reset to defaults</button>
    </form>
</div>
@endsection
