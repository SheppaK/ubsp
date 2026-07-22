@extends('layouts.platform')

@section('title', 'Theme Colors')
@section('header', 'Theme Colors')

@section('content')
<div class="max-w-4xl space-y-6">
    @if(session('success'))
        <div class="alert-success hero-animate">{{ session('success') }}</div>
    @endif

    <p class="text-muted font-sans">Customize the platform brand colors. Changes apply immediately for all users. Use presets or fine-tune individual colors.</p>

    {{-- Live preview --}}
    <div class="bento-card p-6 space-y-4">
        <h3 class="font-heading font-semibold text-heading">Preview</h3>
        <div class="flex flex-wrap gap-3">
            <span class="px-4 py-2 rounded-full bg-brand-coral text-white font-sans text-sm">Primary / Coral</span>
            <span class="px-4 py-2 rounded-full bg-brand-indigo text-brand-cream font-sans text-sm">Indigo</span>
            <span class="px-4 py-2 rounded-full bg-brand-amber text-brand-indigo font-sans text-sm">Accent</span>
            <span class="px-4 py-2 rounded-full bg-brand-lavender text-brand-indigo font-sans text-sm">Lavender</span>
        </div>
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
            <div class="bento-card-dark p-4">
                <p class="font-heading font-bold">Dark hero card</p>
                <p class="text-brand-lavender text-sm mt-1">Subtitle on dark surface</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Stat label</p>
                <p class="stat-value text-brand-coral">42</p>
            </div>
        </div>
    </div>

    {{-- Presets --}}
    @if(count($presets))
        <div class="bento-card p-6">
            <h3 class="font-heading font-semibold text-heading mb-4">Presets</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($presets as $key => $preset)
                    <button type="button"
                        class="preset-btn px-4 py-2 rounded-full border-2 border-brand-lavender dark:border-white/20 font-sans text-sm text-body hover:border-brand-coral transition"
                        data-preset="{{ json_encode(collect($preset)->only([
                            'color_lavender','color_indigo','color_indigo_dark','color_amber',
                            'color_cream','color_coral','color_page_dark','color_surface_dark'
                        ])) }}">
                        {{ $preset['label'] ?? $key }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('platform.theme-settings.update') }}" id="theme-form" class="bento-card p-8 space-y-6 hero-animate">
        @csrf
        @method('PUT')

        <div class="grid sm:grid-cols-2 gap-6">
            @foreach([
                'color_coral' => 'Primary (Coral)',
                'color_indigo' => 'Indigo',
                'color_indigo_dark' => 'Indigo Dark',
                'color_lavender' => 'Lavender',
                'color_amber' => 'Accent (Amber)',
                'color_cream' => 'Cream / Light text',
                'color_page_dark' => 'Dark mode page background',
                'color_surface_dark' => 'Dark mode card background',
            ] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="block font-sans text-sm font-medium text-body mb-1">{{ $label }}</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" id="{{ $field }}_picker" value="{{ old($field, $settings->$field) }}"
                            class="h-10 w-14 rounded-lg border border-brand-lavender/40 cursor-pointer"
                            oninput="document.getElementById('{{ $field }}').value = this.value">
                        <input type="text" name="{{ $field }}" id="{{ $field }}"
                            value="{{ old($field, $settings->$field) }}"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            class="input-field flex-1 font-mono text-sm"
                            required>
                    </div>
                    <x-input-error :messages="$errors->get($field)" class="mt-1" />
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-primary">Save Theme</button>
        </div>
    </form>

    <form method="POST" action="{{ route('platform.theme-settings.reset') }}" onsubmit="return confirm('Reset to default UBSP colors?')">
        @csrf
        <button type="submit" class="btn-ghost text-sm">Reset to Defaults</button>
    </form>
</div>

@push('scripts')
<script>
function hexToRgb(hex) {
    hex = hex.replace('#', '');
    if (hex.length === 3) {
        hex = hex.split('').map(c => c + c).join('');
    }
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    return `${r} ${g} ${b}`;
}

const themeFields = [
    'color_lavender', 'color_indigo', 'color_indigo_dark', 'color_amber',
    'color_cream', 'color_coral', 'color_page_dark', 'color_surface_dark'
];

const cssVarMap = {
    color_lavender: '--color-lavender',
    color_indigo: '--color-indigo',
    color_indigo_dark: '--color-indigo-dark',
    color_amber: '--color-amber',
    color_cream: '--color-cream',
    color_coral: '--color-coral',
    color_page_dark: '--color-page-dark',
    color_surface_dark: '--color-surface-dark',
};

function applyThemePreview() {
    themeFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && input.value) {
            document.documentElement.style.setProperty(cssVarMap[field], hexToRgb(input.value));
        }
    });
}

themeFields.forEach(field => {
    const input = document.getElementById(field);
    const picker = document.getElementById(field + '_picker');
    if (input) input.addEventListener('input', applyThemePreview);
    if (picker) picker.addEventListener('input', applyThemePreview);
});

document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const preset = JSON.parse(btn.dataset.preset);
        Object.entries(preset).forEach(([key, value]) => {
            const input = document.getElementById(key);
            const picker = document.getElementById(key + '_picker');
            if (input) input.value = value;
            if (picker) picker.value = value;
        });
        applyThemePreview();
    });
});
</script>
@endpush
@endsection
