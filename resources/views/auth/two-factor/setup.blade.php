<x-guest-layout>
    <h2 class="text-lg font-semibold mb-4">Set Up Two-Factor Authentication</h2>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Scan this QR code with your authenticator app, then enter the verification code.</p>
    <div class="flex justify-center mb-4">{!! $qrCodeSvg !!}</div>
    <p class="text-xs text-center text-gray-500 mb-4 font-mono">{{ $secret }}</p>
    <form method="POST" action="{{ route('two-factor.enable') }}">
        @csrf
        <div>
            <x-input-label for="code" value="Verification Code" />
            <x-text-input id="code" name="code" class="block mt-1 w-full" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>
        <x-primary-button class="mt-4">Enable 2FA</x-primary-button>
    </form>
</x-guest-layout>
