<x-guest-layout>
    <h2 class="font-heading text-2xl font-bold text-brand-indigo mb-2 hero-animate">{{ __('Complete Payment') }}</h2>
    <p class="font-sans text-sm text-brand-indigo/60 mb-6 hero-animate">Pay for your selected modules to activate your business account.</p>

    @if(session('error'))
        <div class="mb-4 p-4 rounded-2xl bg-brand-coral/10 text-brand-coral font-sans text-sm space-y-3">
            <p>{{ session('error') }}</p>
            @if(session('kcpay_payload_json'))
                <div>
                    <p class="font-heading font-semibold text-brand-indigo mb-1">Payload sent to KC Pay (from database credentials)</p>
                    <pre class="text-xs bg-white/80 text-brand-indigo p-3 rounded-xl overflow-x-auto whitespace-pre-wrap">{{ session('kcpay_payload_json') }}</pre>
                </div>
            @endif
            @if(session('kcpay_response_json'))
                <div>
                    <p class="font-heading font-semibold text-brand-indigo mb-1">KC Pay response</p>
                    <pre class="text-xs bg-white/80 text-brand-indigo p-3 rounded-xl overflow-x-auto whitespace-pre-wrap">{{ session('kcpay_response_json') }}</pre>
                </div>
            @endif
            <p class="text-xs text-brand-indigo/60">Full details are also in <code>storage/logs/laravel.log</code> under <code>[KC Pay]</code>.</p>
        </div>
    @endif

    @if($simulating ?? false)
        <div class="mb-4 p-4 rounded-2xl bg-brand-amber/15 text-brand-indigo font-sans text-sm">
            <strong>Local simulation is ON.</strong> No payment prompt will appear on your phone.
            Disable “Simulate payments locally” in Admin → KC Pay Settings to send a real mobile money USSD prompt.
        </div>
    @endif

    @if(! $kcpayReady)
        <div class="bento-card p-6 mb-6 font-sans text-brand-indigo/80">
            KC Pay is not configured yet. Please contact the platform administrator.
        </div>
    @endif

    <div class="bento-card p-6 mb-6 space-y-4 hero-animate">
        <div>
            <p class="text-xs uppercase tracking-wide text-brand-indigo/50 font-sans">Business</p>
            <p class="font-heading font-semibold text-brand-indigo">{{ $registration['business_name'] ?? '' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-brand-indigo/50 font-sans mb-2">Selected modules</p>
            <ul class="space-y-2">
                @foreach($pricedModules as $mod)
                    <li class="flex justify-between font-sans text-sm">
                        <span class="text-brand-indigo">{{ $mod->name }}</span>
                        <span class="font-medium text-brand-indigo">K {{ number_format((float) $mod->price_zmw, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="pt-3 border-t border-brand-lavender/30 flex justify-between items-center">
            <span class="font-heading font-bold text-brand-indigo">Total</span>
            <span class="font-heading text-xl font-bold text-brand-coral">K {{ number_format((float) $payment->amount_zmw, 2) }} ZMW</span>
        </div>
        <p class="text-xs text-brand-indigo/50 font-sans">Reference: {{ $payment->seller_reference }}</p>
    </div>

    @if($kcpayReady)
        <form method="POST" action="{{ route('register.business.pay', $payment) }}" class="space-y-5 hero-animate" x-data="{ method: '{{ old('payment_method', 'mobilemoney') }}' }">
            @csrf

            <div>
                <x-input-label value="Payment method" />
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <label class="p-4 rounded-2xl border cursor-pointer transition" :class="method === 'mobilemoney' ? 'border-brand-coral bg-brand-coral/5' : 'border-brand-lavender/40'">
                        <input type="radio" name="payment_method" value="mobilemoney" class="sr-only" x-model="method">
                        <span class="font-heading font-semibold text-brand-indigo block">Mobile Money</span>
                        <span class="font-sans text-xs text-brand-indigo/60">MTN (096/076), Airtel (097/077), Zamtel (095/075)</span>
                    </label>
                    @if($kcpayCardReady)
                        <label class="p-4 rounded-2xl border cursor-pointer transition" :class="method === 'card' ? 'border-brand-coral bg-brand-coral/5' : 'border-brand-lavender/40'">
                            <input type="radio" name="payment_method" value="card" class="sr-only" x-model="method">
                            <span class="font-heading font-semibold text-brand-indigo block">Card</span>
                            <span class="font-sans text-xs text-brand-indigo/60">Visa / Mastercard</span>
                        </label>
                    @else
                        <div class="p-4 rounded-2xl border border-brand-lavender/30 opacity-60">
                            <span class="font-heading font-semibold text-brand-indigo block">Card</span>
                            <span class="font-sans text-xs text-brand-indigo/60">Unavailable — admin must configure public & private keys</span>
                        </div>
                    @endif
                </div>
            </div>

            <div x-show="method === 'mobilemoney'" x-cloak class="space-y-4">
                <div>
                    <x-input-label for="network" value="Mobile network" />
                    <select id="network" name="network" class="block mt-1 w-full rounded-2xl border-brand-lavender/40 font-sans">
                        <option value="mtn" @selected(old('network') === 'mtn')>MTN</option>
                        <option value="airtel" @selected(old('network') === 'airtel')>Airtel</option>
                        <option value="zamtel" @selected(old('network') === 'zamtel')>Zamtel</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="mobile_phone" value="Mobile number" />
                    <x-text-input id="mobile_phone" name="mobile_phone" class="block mt-1 w-full" :value="old('mobile_phone', $registration['phone'] ?? '')" placeholder="0977123456" />
                    <p class="mt-1 text-xs text-brand-indigo/50 font-sans">Leading 0 is stripped automatically (e.g. 0971234567 → 971234567).</p>
                    <x-input-error :messages="$errors->get('mobile_phone')" class="mt-2" />
                </div>
            </div>

            <x-primary-button class="w-full justify-center">{{ __('Pay K ') }}{{ number_format((float) $payment->amount_zmw, 2) }}</x-primary-button>
        </form>
    @endif
</x-guest-layout>
