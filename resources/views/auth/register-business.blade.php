<x-guest-layout>
    <h2 class="font-heading text-2xl font-bold text-brand-indigo mb-2 hero-animate">{{ __('Register Your Business') }}</h2>
    <p class="font-sans text-sm text-brand-indigo/60 mb-6 hero-animate">Create a business account and choose the modules you want to use.</p>

    <form method="POST" action="{{ route('register.business') }}" class="hero-animate space-y-5">
        @csrf

        <div>
            <x-input-label for="business_name" :value="__('Business Name')" />
            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autofocus />
            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="owner_name" :value="__('Your Full Name')" />
            <x-text-input id="owner_name" class="block mt-1 w-full" type="text" name="owner_name" :value="old('owner_name')" required />
            <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="address" :value="__('Business Address')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>
        </div>

        <div>
            <x-input-label :value="__('Select Modules')" />
            <p class="font-sans text-xs text-brand-indigo/50 mb-3">Choose at least one module. You can manage users and data within each module.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($modules as $slug => $mod)
                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-brand-lavender/40 hover:border-brand-coral/50 cursor-pointer transition">
                        <input type="checkbox" name="modules[]" value="{{ $slug }}" class="mt-1 rounded border-brand-lavender text-brand-coral focus:ring-brand-coral"
                            {{ in_array($slug, old('modules', ['boarding-house'])) ? 'checked' : '' }}>
                        <span>
                            <span class="font-heading font-semibold text-brand-indigo block">{{ $mod['name'] }}</span>
                            <span class="font-sans text-xs text-brand-indigo/60">{{ $mod['description'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('modules')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>
            <x-primary-button>{{ __('Create Business Account') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
