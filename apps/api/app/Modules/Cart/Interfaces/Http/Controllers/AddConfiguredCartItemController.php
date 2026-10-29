<?php

namespace App\Modules\Cart\Interfaces\Http\Controllers;

use App\Modules\Cart\Application\AddConfiguredCartItem;
use App\Modules\Cart\Application\GuestCartSession;
use App\Modules\Cart\Domain\CartFailure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

final readonly class AddConfiguredCartItemController
{
    public function __construct(private AddConfiguredCartItem $addItem, private GuestCartSession $sessions) {}

    public function __invoke(Request $request): JsonResponse
    {
        $correlation = (string) Str::uuid();
        $body = $request->getContent();
        $key = (string) $request->header('Idempotency-Key', '');
        if (strlen($body) > 16384) {
            return $this->error('invalid_request', 413, $correlation);
        }
        if (strtolower(trim(explode(';', (string) $request->header('Content-Type'), 2)[0])) !== 'application/json'
            || preg_match('/^[A-Za-z0-9._~-]{20,120}$/', $key) !== 1) {
            return $this->error('invalid_request', 400, $correlation);
        }
        try {
            $input = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return $this->error('invalid_request', 400, $correlation);
        }
        if (! is_array($input) || array_is_list($input) || array_diff(array_keys($input), ['product_slug', 'model_key', 'quantity', 'quote_snapshot_id', 'configuration']) !== []
            || ! is_string($input['product_slug'] ?? null) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $input['product_slug']) !== 1
            || ! is_string($input['model_key'] ?? null) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $input['model_key']) !== 1
            || ! is_int($input['quantity'] ?? null) || $input['quantity'] < 1 || $input['quantity'] > 10000
            || ! is_string($input['quote_snapshot_id'] ?? null) || preg_match('/^[a-f0-9]{64}$/', $input['quote_snapshot_id']) !== 1
            || ! is_array($input['configuration'] ?? null) || array_is_list($input['configuration'])) {
            return $this->error('invalid_request', 400, $correlation);
        }
        try {
            $this->sessions->bindSubmission((string) $request->attributes->get('cart_session_reference'), $key, $input);
            $result = $this->addItem->execute($input, (string) $request->attributes->get('cart_owner_hash'), $key);

            return response()->json($result['result'], $result['replay'] ? 200 : 201)->header('Cache-Control', 'no-store, private');
        } catch (CartFailure $failure) {
            return $this->error($failure->publicCode, $failure->httpStatus, $correlation);
        } catch (Throwable) {
            return $this->error('temporarily_unavailable', 503, $correlation);
        }
    }

    private function error(string $code, int $status, string $correlation): JsonResponse
    {
        $messages = [
            'invalid_request' => 'Review the information provided.', 'unsupported_mode' => 'This product cannot be added through this flow.',
            'invalid_configuration' => 'Complete or review the required product details.', 'stale_quote' => 'The price changed. Review the new quote and submit again.',
            'unavailable' => 'This product or model is no longer available.', 'idempotency_conflict' => 'This submission key was already used for different details.',
            'temporarily_unavailable' => 'The cart is temporarily unavailable.',
            'csrf_failed' => 'The request could not be verified.',
        ];

        return response()->json(['error' => ['code' => $code, 'message' => $messages[$code] ?? 'Request failed.', 'correlation_id' => $correlation]], $status)
            ->header('Cache-Control', 'no-store, private');
    }
}
