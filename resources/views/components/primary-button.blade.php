<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary w-full sm:w-auto text-sm']) }}>
    {{ $slot }}
</button>
