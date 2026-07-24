<?php

namespace App\Services;

use App\Models\KcpaySetting;
use App\Models\PlatformModule;
use App\Models\RegistrationPayment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KcpayService
{
    public const UNREACHABLE_MESSAGE = 'Cannot reach the KC Pay server from this machine. The API host (productcheckout.kundananjicreations.com) timed out. Try a different network (mobile hotspot), check your firewall, or contact Kundananji Creations support.';

    /**
     * Always load active credentials from the database (never .env).
     */
    public function settings(): ?KcpaySetting
    {
        return KcpaySetting::query()
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    public function isReady(): bool
    {
        $settings = $this->settings();

        return $settings && $settings->isConfigured();
    }

    public function isReadyForCard(): bool
    {
        $settings = $this->settings();

        return $this->isReady()
            && filled($settings?->public_key)
            && filled($settings?->getDecryptedPrivateKey());
    }

    /**
     * Normalize base URL so it always ends with a single trailing slash.
     * Source: kcpay_settings.base_url in the database.
     */
    public function baseUrl(?string $override = null): string
    {
        $settings = $this->settings();
        $url = $override
            ?? $settings?->base_url
            ?? 'https://productcheckout.kundananjicreations.com/';

        return rtrim($url, '/').'/';
    }

    /**
     * Build a copy of the payload safe for logs/UI (password redacted).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function payloadForDisplay(array $payload): array
    {
        $display = $payload;

        if (array_key_exists('apiPassword', $display)) {
            $raw = (string) ($display['apiPassword'] ?? '');
            $display['apiPassword'] = $raw === ''
                ? '[EMPTY — check DB kcpay_settings.api_password]'
                : '[REDACTED length='.strlen($raw).']';
        }

        $display['_credential_source'] = 'database:kcpay_settings';

        return $display;
    }

    /**
     * Pretty JSON for logs / flash messages.
     *
     * @param  array<string, mixed>  $payload
     */
    public function payloadJson(array $payload): string
    {
        return json_encode(
            $this->payloadForDisplay($payload),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '{}';
    }

    /**
     * POST https://productcheckout.kundananjicreations.com/
     */
    public function initiateEndpoint(?string $override = null): string
    {
        return $this->baseUrl($override);
    }

    /**
     * POST https://productcheckout.kundananjicreations.com/request-status
     */
    public function requestStatusEndpoint(?string $override = null): string
    {
        return $this->baseUrl($override).'request-status';
    }

    /**
     * GET https://productcheckout.kundananjicreations.com/pay-for-product?transactionToken={token}
     */
    public function cardPaymentEndpoint(string $token, ?string $override = null): string
    {
        return $this->baseUrl($override).'pay-for-product?transactionToken='.urlencode($token);
    }

    /**
     * @param  list<string>  $moduleSlugs
     */
    public function calculateTotal(array $moduleSlugs): float
    {
        if ($moduleSlugs === []) {
            return 0.0;
        }

        return (float) PlatformModule::query()
            ->whereIn('slug', $moduleSlugs)
            ->where('is_enabled', true)
            ->sum('price_zmw');
    }

    /**
     * @param  list<string>  $moduleSlugs
     * @return Collection<int, PlatformModule>
     */
    public function pricedModules(array $moduleSlugs): Collection
    {
        return PlatformModule::query()
            ->whereIn('slug', $moduleSlugs)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return array{success: bool, token?: string, transaction_id?: string, payment_url?: string, message?: string, raw?: array, payload?: array}
     */
    public function initiate(RegistrationPayment $payment, array $customer, string $paymentMethod, ?string $network = null, ?string $phone = null): array
    {
        $settings = $this->settings();

        if (! $settings || ! $settings->isConfigured()) {
            Log::error('[KC Pay] Credentials missing in database (kcpay_settings). No .env credentials are used.');

            return [
                'success' => false,
                'message' => 'KC Pay is not configured in the database. Save credentials under Admin → KC Pay Settings.',
            ];
        }

        if ($paymentMethod === 'card' && ! $this->isReadyForCard()) {
            return ['success' => false, 'message' => 'Card payments require public and private keys in KC Pay settings.'];
        }

        $endpoint = $this->initiateEndpoint();
        $formattedPhone = $phone ? $this->formatZambianPhone($phone) : null;

        // Credentials + fields from database kcpay_settings only (not .env).
        $payload = [
            'apiUserName' => $settings->api_username,
            'apiPassword' => $settings->getDecryptedApiPassword(),
            'amount' => round((float) $payment->amount_zmw, 2),
            'paymentMethod' => $paymentMethod,
            'source' => $settings->product_reference,
            'country' => 'zm',
            'currency' => 'ZMW',
            'sellerReference' => $payment->seller_reference,
            'products' => [
                [
                    'productReference' => $settings->product_reference,
                    'quantity' => 1,
                    'amount' => round((float) $payment->amount_zmw, 2),
                ],
            ],
            'customerName' => $customer['owner_name'] ?? null,
            'customerEmail' => $customer['email'] ?? null,
            'userId' => (string) $payment->id,
            'mode' => $settings->apiMode(),
        ];

        if ($paymentMethod === 'mobilemoney') {
            if (! $formattedPhone || ! $network) {
                return ['success' => false, 'message' => 'Mobile money requires a valid phone number and network (mtn, airtel, or zamtel).'];
            }

            $payload['customerPhoneNumber'] = $formattedPhone;
            $payload['network'] = strtolower($network);
            $payload['accountNo'] = $formattedPhone;
        }

        $payloadJson = $this->payloadJson($payload);

        Log::info("[KC Pay] Initiate request (credentials from database)\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}", [
            'settings_id' => $settings->id,
            'credential_source' => 'database:kcpay_settings',
            'reference' => $payment->seller_reference,
        ]);

        if ($settings->shouldSimulateLocally()) {
            Log::warning('[KC Pay] Local simulation enabled — live API will not be called.', [
                'payload' => $this->payloadForDisplay($payload),
            ]);

            $sim = $this->simulateInitiate($payment, $paymentMethod);
            $sim['payload'] = $this->payloadForDisplay($payload);

            return $sim;
        }

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            $body = $response->json() ?? [];
            $responseJson = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';

            Log::info("[KC Pay] Initiate response\nHTTP: {$response->status()}\nBody JSON:\n{$responseJson}", [
                'reference' => $payment->seller_reference,
            ]);

            if (! $response->successful()) {
                Log::error("[KC Pay] Initiate HTTP error\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}\nResponse JSON:\n{$responseJson}");

                return [
                    'success' => false,
                    'message' => $body['message'] ?? 'KC Pay rejected the payment request.',
                    'raw' => $body,
                    'payload' => $this->payloadForDisplay($payload),
                ];
            }

            $status = strtolower((string) ($body['status'] ?? ''));

            if (! in_array($status, ['success', 'successful'], true)) {
                Log::error("[KC Pay] Initiate rejected by gateway\nPayload JSON:\n{$payloadJson}\nResponse JSON:\n{$responseJson}");

                return [
                    'success' => false,
                    'message' => $body['message'] ?? 'Unable to initiate payment.',
                    'raw' => $body,
                    'payload' => $this->payloadForDisplay($payload),
                ];
            }

            $token = $body['token'] ?? null;
            $result = [
                'success' => true,
                'token' => $token,
                'transaction_id' => $body['transactionId'] ?? null,
                'raw' => $body,
                'payload' => $this->payloadForDisplay($payload),
            ];

            if ($paymentMethod === 'card' && $token) {
                $result['payment_url'] = $this->cardPaymentEndpoint((string) $token);
            }

            return $result;
        } catch (ConnectionException $e) {
            Log::error("[KC Pay] Initiate connection error: {$e->getMessage()}\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}", [
                'reference' => $payment->seller_reference,
                'credential_source' => 'database:kcpay_settings',
                'settings_id' => $settings->id,
            ]);

            return [
                'success' => false,
                'message' => self::UNREACHABLE_MESSAGE,
                'payload' => $this->payloadForDisplay($payload),
            ];
        } catch (\Throwable $e) {
            Log::error("[KC Pay] Initiate failed: {$e->getMessage()}\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}", [
                'reference' => $payment->seller_reference,
            ]);

            return [
                'success' => false,
                'message' => 'Could not reach KC Pay. Please try again shortly.',
                'payload' => $this->payloadForDisplay($payload),
            ];
        }
    }

    /**
     * @return array{success: bool, status?: string, message?: string, raw?: array, payload?: array}
     */
    public function checkStatus(RegistrationPayment $payment): array
    {
        $settings = $this->settings();

        if (! $settings || ! $settings->isConfigured()) {
            return ['success' => false, 'message' => 'KC Pay is not configured in the database.', 'status' => 'pending'];
        }

        if ($settings->shouldSimulateLocally() || $this->isSimulatedToken($payment->token)) {
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Simulated payment completed',
                'raw' => [
                    'status' => 'Success',
                    'sellerReference' => $payment->seller_reference,
                    'transactionId' => $payment->transaction_id ?? 'SIM-TXN',
                    'token' => $payment->token,
                    'simulated' => true,
                ],
            ];
        }

        $endpoint = $this->requestStatusEndpoint();

        $payload = [
            'apiUserName' => $settings->api_username,
            'apiPassword' => $settings->getDecryptedApiPassword(),
        ];

        // Prefer token, then sellerReference, then transactionId (per docs).
        if ($payment->token) {
            $payload['token'] = $payment->token;
        } elseif ($payment->seller_reference) {
            $payload['sellerReference'] = $payment->seller_reference;
        } elseif ($payment->transaction_id) {
            $payload['transactionId'] = $payment->transaction_id;
        }

        $payloadJson = $this->payloadJson($payload);

        Log::info("[KC Pay] Status request (credentials from database)\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}", [
            'settings_id' => $settings->id,
            'credential_source' => 'database:kcpay_settings',
        ]);

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            $body = $response->json() ?? [];
            $responseJson = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';

            Log::info("[KC Pay] Status response\nHTTP: {$response->status()}\nBody JSON:\n{$responseJson}");

            $status = strtolower((string) ($body['status'] ?? ''));
            $message = strtolower((string) ($body['message'] ?? ''));

            // Docs: "transaction not found" may still be propagating — treat as pending.
            if ($this->isNotFoundYet($status, $message)) {
                return [
                    'success' => true,
                    'status' => 'pending',
                    'message' => $body['message'] ?? 'Transaction not found yet',
                    'raw' => $body,
                    'payload' => $this->payloadForDisplay($payload),
                ];
            }

            if ($status === '') {
                $status = 'pending';
            }

            return [
                'success' => $response->successful() || in_array($status, ['success', 'successful', 'pending', 'processing', 'failed'], true),
                'status' => $status,
                'message' => $body['message'] ?? null,
                'raw' => $body,
                'payload' => $this->payloadForDisplay($payload),
            ];
        } catch (ConnectionException $e) {
            Log::error("[KC Pay] Status check connection error: {$e->getMessage()}\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}");

            return [
                'success' => false,
                'message' => self::UNREACHABLE_MESSAGE,
                'status' => 'pending',
                'payload' => $this->payloadForDisplay($payload),
            ];
        } catch (\Throwable $e) {
            Log::error("[KC Pay] Status check failed: {$e->getMessage()}\nEndpoint: {$endpoint}\nPayload JSON:\n{$payloadJson}");

            return [
                'success' => false,
                'message' => 'Status check failed.',
                'status' => 'pending',
                'payload' => $this->payloadForDisplay($payload),
            ];
        }
    }

    public function isSuccessStatus(?string $status): bool
    {
        return in_array(strtolower((string) $status), ['success', 'successful'], true);
    }

    public function isFailedStatus(?string $status): bool
    {
        return in_array(strtolower((string) $status), ['failed', 'failure', 'cancelled', 'canceled'], true);
    }

    public function isPendingStatus(?string $status): bool
    {
        return in_array(strtolower((string) $status), ['pending', 'processing', ''], true);
    }

    protected function isNotFoundYet(?string $status, ?string $message): bool
    {
        $haystack = strtolower(trim(($status ?? '').' '.($message ?? '')));

        return str_contains($haystack, 'not found')
            || str_contains($haystack, 'transaction not found');
    }

    /**
     * Strip country code / leading 0 per KC Pay docs (0971234567 → 971234567).
     */
    public function formatZambianPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '260')) {
            $digits = substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    /**
     * @return array{success: bool, token: string, transaction_id: string, payment_url?: string, message: string, raw: array}
     */
    protected function simulateInitiate(RegistrationPayment $payment, string $paymentMethod): array
    {
        $token = 'sim_'.bin2hex(random_bytes(8));
        $transactionId = 'SIM-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        Log::warning('[KC Pay] Using local simulation (API unreachable or simulate_locally enabled)', [
            'reference' => $payment->seller_reference,
        ]);

        $result = [
            'success' => true,
            'token' => $token,
            'transaction_id' => $transactionId,
            'message' => 'Simulated payment initiated (local test mode).',
            'raw' => [
                'status' => 'Success',
                'message' => 'Simulated transaction initiated successfully',
                'token' => $token,
                'transactionId' => $transactionId,
                'simulated' => true,
            ],
        ];

        if ($paymentMethod === 'card') {
            $result['payment_url'] = route('register.business.return', $payment);
        }

        return $result;
    }

    protected function isSimulatedToken(?string $token): bool
    {
        return is_string($token) && str_starts_with($token, 'sim_');
    }

    public function callbackUrl(): string
    {
        $settings = $this->settings();

        if ($settings?->callback_url) {
            return $settings->callback_url;
        }

        return url('/api/kcpay/callback');
    }

    /**
     * @return array{success: bool, message: string, latency_ms?: int, http_status?: int}
     */
    public function testConnection(?string $baseUrl = null): array
    {
        $settings = $this->settings();
        $url = $this->initiateEndpoint($baseUrl);

        $started = microtime(true);

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($url, []);

            $latency = (int) round((microtime(true) - $started) * 1000);
            $body = $response->json() ?? [];

            Log::info('[KC Pay] Connection test', [
                'endpoint' => $url,
                'http' => $response->status(),
                'body' => $body,
                'credential_source' => 'database:kcpay_settings',
                'settings_id' => $settings?->id,
            ]);

            if ($response->successful() || $response->clientError()) {
                return [
                    'success' => true,
                    'message' => 'Connected to KC Pay API ('.$latency.' ms). Server responded: '.($body['message'] ?? 'OK'),
                    'latency_ms' => $latency,
                    'http_status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'message' => 'KC Pay responded with HTTP '.$response->status().': '.($body['message'] ?? 'Unexpected response'),
                'latency_ms' => $latency,
                'http_status' => $response->status(),
            ];
        } catch (ConnectionException $e) {
            Log::error('[KC Pay] Connection test failed: '.$e->getMessage(), [
                'endpoint' => $url,
            ]);

            return [
                'success' => false,
                'message' => self::UNREACHABLE_MESSAGE,
            ];
        } catch (\Throwable $e) {
            Log::error('[KC Pay] Connection test failed: '.$e->getMessage(), [
                'endpoint' => $url,
            ]);

            return [
                'success' => false,
                'message' => 'Connection test failed: '.$e->getMessage(),
            ];
        }
    }
}
