<x-guest-layout>
    <div class="text-center hero-animate" id="payment-waiting" data-status-url="{{ route('register.business.status', $payment) }}">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full border-4 border-brand-lavender border-t-brand-coral animate-spin"></div>
        <h2 class="font-heading text-2xl font-bold text-brand-indigo mb-2">Waiting for payment</h2>
        <p class="font-sans text-sm text-brand-indigo/60 mb-4">Approve the mobile money prompt on your phone. This page will update automatically.</p>
        <p class="font-sans text-xs text-brand-indigo/50">Reference: {{ $payment->seller_reference }}</p>
        <p id="payment-status-message" class="mt-4 font-sans text-sm text-brand-coral hidden"></p>
        <a id="payment-retry" href="{{ route('register.business.checkout', $payment) }}" class="mt-4 inline-block text-sm text-brand-coral hidden">Try again</a>
    </div>

    @push('scripts')
    <script>
        (function () {
            const root = document.getElementById('payment-waiting');
            if (!root) return;

            const statusUrl = root.dataset.statusUrl;
            const messageEl = document.getElementById('payment-status-message');
            const retryEl = document.getElementById('payment-retry');
            let attempts = 0;
            // KC Pay docs: poll every 5 seconds for up to 2 minutes.
            const maxAttempts = 24;
            const intervalMs = 5000;

            async function poll() {
                if (attempts >= maxAttempts) {
                    messageEl.textContent = 'Payment is taking longer than expected. You can wait or try again.';
                    messageEl.classList.remove('hidden');
                    retryEl.classList.remove('hidden');
                    return;
                }

                attempts++;

                try {
                    const response = await fetch(statusUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const data = await response.json();

                    if (data.status === 'paid' && data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (data.status === 'failed') {
                        messageEl.textContent = data.message || 'Payment failed.';
                        messageEl.classList.remove('hidden');
                        retryEl.classList.remove('hidden');
                        return;
                    }
                } catch (e) {
                    // keep polling — docs treat transient errors as pending
                }

                setTimeout(poll, intervalMs);
            }

            // First poll after a short delay so the transaction can propagate.
            setTimeout(poll, intervalMs);
        })();
    </script>
    @endpush
</x-guest-layout>
