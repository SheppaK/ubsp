<x-guest-layout>
    <h2 class="font-heading text-2xl font-bold text-brand-indigo mb-4 hero-animate">{{ __('Reset Password') }}</h2>
    <p class="mb-6 text-sm font-sans text-brand-indigo/70 hero-animate">
        {{ __('Forgot your password? Enter your email and we will send you a reset link.') }}
    </p>

    <x-auth-session-status class="mb-4 font-sans text-brand-indigo/70" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="hero-animate space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>{{ __('Email Password Reset Link') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
