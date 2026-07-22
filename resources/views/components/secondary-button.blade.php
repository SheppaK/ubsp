<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-ghost text-sm']) }}>
    {{ $slot }}
</button>
