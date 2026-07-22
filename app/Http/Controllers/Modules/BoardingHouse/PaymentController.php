<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Payment;
use App\Services\Modules\BoardingHouse\StripePaymentService;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected StripePaymentService $stripe,
    ) {}

    public function checkout(Payment $payment): RedirectResponse
    {
        abort_unless($payment->bookingRequest->user_id === auth()->id(), 403);

        $url = $this->stripe->checkoutUrl($payment);

        if (! $url) {
            return back()->withErrors(['payment' => 'Payment is not available. Stripe may not be configured.']);
        }

        return redirect()->away($url);
    }

    public function success(Request $request, Payment $payment): View|RedirectResponse
    {
        abort_unless($payment->bookingRequest->user_id === auth()->id(), 403);

        if ($request->filled('session_id')) {
            $this->stripe->verifySession($payment, $request->session_id);
        }

        return view('modules.boarding-house.payments.success', [
            'config' => $this->modules->get('boarding-house'),
            'payment' => $payment->fresh(),
        ]);
    }
}
