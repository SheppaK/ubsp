<x-guest-layout>
    <h2 class="text-lg font-semibold mb-4">Two-Factor Authentication</h2>
    @if($user->two_factor_enabled)
        <p class="text-sm text-emerald-600 mb-4">Two-factor authentication is enabled.</p>
        <form method="POST" action="{{ route('two-factor.disable') }}">
            @csrf @method('DELETE')
            <div class="mt-4">
                <x-input-label for="password" value="Confirm Password" />
                <x-text-input id="password" type="password" name="password" class="block mt-1 w-full" required />
            </div>
            <x-danger-button class="mt-4">Disable 2FA</x-danger-button>
        </form>
    @else
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">2FA is not enabled.</p>
        <a href="{{ route('two-factor.show') }}" class="text-indigo-600 hover:underline">Set up two-factor authentication</a>
    @endif
</x-guest-layout>
