<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformModule;
use App\Models\RegistrationPayment;
use App\Services\KcpayService;
use App\Services\ModuleManager;
use App\Services\RegistrationPaymentService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class BusinessRegistrationController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected RegistrationPaymentService $payments,
        protected KcpayService $kcpay,
    ) {}

    public function create(): View
    {
        if (request()->hasSession()) {
            request()->session()->regenerateToken();
        }

        $moduleRecords = PlatformModule::query()->where('is_enabled', true)->orderBy('sort_order')->get();
        $modules = $this->modules->enabled()->filter(fn ($mod, $slug) => $moduleRecords->contains('slug', $slug));

        return view('auth.register-business', [
            'modules' => $modules,
            'modulePrices' => $moduleRecords->pluck('price_zmw', 'slug'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $enabledSlugs = PlatformModule::query()->where('is_enabled', true)->pluck('slug')->all();

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['string', 'in:'.implode(',', $enabledSlugs)],
        ]);

        $payment = $this->payments->createPendingPayment($validated);
        $user = $payment->user;

        if ($user) {
            event(new Registered($user));
            Auth::login($user);
        }

        if ($payment->isPaid()) {
            $business = $payment->business;

            return redirect()
                ->route('platform.business.dashboard')
                ->with('success', 'Welcome! Your business "'.($business->name ?? '').'" is ready.');
        }

        return redirect()
            ->route('platform.business.payment')
            ->with('status', 'Account created. Please complete payment to unlock your modules.');
    }

    public function checkout(RegistrationPayment $payment): View|RedirectResponse
    {
        if ($payment->isPaid() && $payment->business_id) {
            return redirect()->route('login')->with('status', 'This registration is already complete. Please log in.');
        }

        $data = $payment->getRegistrationData();
        $pricedModules = $this->kcpay->pricedModules($payment->modules ?? []);

        return view('auth.register-business-checkout', [
            'payment' => $payment,
            'registration' => $data,
            'pricedModules' => $pricedModules,
            'kcpayReady' => $this->kcpay->isReady(),
            'kcpayCardReady' => $this->kcpay->isReadyForCard(),
            'simulating' => (bool) $this->kcpay->settings()?->shouldSimulateLocally(),
        ]);
    }

    public function pay(Request $request, RegistrationPayment $payment): RedirectResponse
    {
        if ($payment->isPaid()) {
            return redirect()->route('register.business.success', $payment);
        }

        if (! $this->kcpay->isReady()) {
            return back()->with('error', 'KC Pay is not configured yet. Please contact the platform administrator.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:mobilemoney,card'],
            'network' => ['required_if:payment_method,mobilemoney', 'nullable', 'in:mtn,airtel,zamtel'],
            'mobile_phone' => ['required_if:payment_method,mobilemoney', 'nullable', 'string', 'max:20'],
        ]);

        if ($validated['payment_method'] === 'card' && ! $this->kcpay->isReadyForCard()) {
            return back()->with('error', 'Card payments require public and private keys in KC Pay settings.');
        }

        // Docs: sellerReference must be unique for every transaction (including retries).
        $payment = $this->payments->refreshSellerReference($payment);

        $data = $payment->getRegistrationData();
        $phone = $validated['mobile_phone'] ?? ($data['phone'] ?? null);

        $result = $this->kcpay->initiate(
            $payment,
            $data,
            $validated['payment_method'],
            $validated['network'] ?? null,
            $phone,
        );

        if (! ($result['success'] ?? false)) {
            $payloadJson = ! empty($result['payload'])
                ? json_encode($result['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : null;

            return back()
                ->with('error', $result['message'] ?? 'Payment could not be started.')
                ->with('kcpay_payload_json', $payloadJson)
                ->with('kcpay_response_json', ! empty($result['raw'])
                    ? json_encode($result['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : null);
        }

        $payment->update([
            'status' => 'processing',
            'payment_method' => $validated['payment_method'],
            'network' => $validated['network'] ?? null,
            'phone' => $phone,
            'token' => $result['token'] ?? null,
            'transaction_id' => $result['transaction_id'] ?? null,
            'kcpay_init_response' => [
                'response' => $result['raw'] ?? null,
                'request_payload' => $result['payload'] ?? null,
                'credential_source' => 'database:kcpay_settings',
            ],
        ]);

        if ($validated['payment_method'] === 'card' && ! empty($result['payment_url'])) {
            return redirect()->away($result['payment_url']);
        }

        return redirect()->route('register.business.waiting', $payment);
    }

    public function waiting(RegistrationPayment $payment): View|RedirectResponse
    {
        if ($payment->isPaid()) {
            return redirect()->route('register.business.success', $payment);
        }

        return view('auth.register-business-waiting', [
            'payment' => $payment,
        ]);
    }

    public function status(RegistrationPayment $payment): JsonResponse
    {
        $payment = $this->payments->syncFromGateway($payment);

        if ($payment->isPaid()) {
            $this->payments->fulfillIfPaid($payment);

            return response()->json([
                'status' => 'paid',
                'redirect' => route('register.business.success', $payment),
            ]);
        }

        if ($payment->status === 'failed') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed or was cancelled. You can try again.',
            ]);
        }

        return response()->json([
            'status' => $payment->status,
        ]);
    }

    public function success(RegistrationPayment $payment): RedirectResponse
    {
        $payment = $this->payments->syncFromGateway($payment);

        if (! $payment->isPaid()) {
            return redirect()
                ->route('register.business.checkout', $payment)
                ->with('error', 'Payment not confirmed yet. Please complete payment or wait a moment.');
        }

        $user = $this->payments->loginRegisteredUser($payment);

        if (! $user) {
            return redirect()->route('login')->with('status', 'Registration complete. Please log in with your credentials.');
        }

        return redirect()
            ->route('platform.business.dashboard')
            ->with('success', 'Payment received! Your modules are now unlocked.');
    }

    public function return(RegistrationPayment $payment): RedirectResponse
    {
        return redirect()->route('register.business.success', $payment);
    }
}
