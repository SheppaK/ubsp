<x-guest-layout>
    <h2 class="font-heading text-2xl font-bold text-brand-indigo mb-6 hero-animate">{{ __('Create Account') }}</h2>

    <form method="POST" action="{{ route('register') }}" class="hero-animate space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex flex-col gap-1">
                <a class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <a class="text-sm font-sans text-brand-coral hover:underline transition" href="{{ route('register.business') }}">
                    {{ __('Register as a business owner') }}
                </a>
            </div>
            <x-primary-button>{{ __('Register') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
