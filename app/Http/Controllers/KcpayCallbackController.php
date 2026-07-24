<?php

namespace App\Http\Controllers;

use App\Services\RegistrationPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class KcpayCallbackController extends Controller
{
    public function __construct(protected RegistrationPaymentService $payments) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();

            // Also accept raw JSON body if form parsing is empty.
            if ($payload === []) {
                $payload = $request->json()->all() ?: [];
            }

            Log::info('[KC Pay] Webhook received', $payload);

            $processed = $this->payments->handleCallback($payload);

            if (! $processed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            Log::error('[KC Pay] Webhook error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal error',
            ], 500);
        }
    }
}
