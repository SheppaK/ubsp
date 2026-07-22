<x-guest-layout>
    <h2 class="text-lg font-semibold mb-4">Two-Factor Challenge</h2>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Enter the code from your authenticator app.</p>
    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf
        <div>
            <x-input-label for="code" value="Verification Code" />
            <x-text-input id="code" name="code" class="block mt-1 w-full" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>
        <x-primary-button class="mt-4">Verify</x-primary-button>
    </form>
</x-guest-layout>
