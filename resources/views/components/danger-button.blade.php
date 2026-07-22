<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-coral text-white font-sans font-semibold rounded-full hover:brightness-110 transition']) }}>
    {{ $slot }}
</button>
