@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-sans text-sm font-medium text-brand-indigo dark:text-brand-cream']) }}>
    {{ $value ?? $slot }}
</label>
