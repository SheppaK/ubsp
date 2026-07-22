@php
    try {
        $theme = app(\App\Services\ThemeService::class)->current();
        $cssVars = [];
        foreach ($theme->toCssVariables() as $name => $value) {
            $cssVars[] = "{$name}: {$value};";
        }
    } catch (\Throwable) {
        $cssVars = [
            '--color-lavender: 184 184 209;',
            '--color-indigo: 91 95 151;',
            '--color-indigo-dark: 74 77 122;',
            '--color-amber: 255 193 69;',
            '--color-cream: 255 255 251;',
            '--color-coral: 255 107 108;',
            '--color-page-dark: 42 45 82;',
            '--color-surface-dark: 69 73 122;',
        ];
    }
@endphp
<style id="ubsp-theme-vars">
    html:root,
    :root,
    .dark {
        {!! implode("\n        ", $cssVars) !!}
    }
</style>
