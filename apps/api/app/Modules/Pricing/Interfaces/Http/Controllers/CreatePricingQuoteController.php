<?php

namespace App\Modules\Pricing\Interfaces\Http\Controllers;

use App\Modules\Pricing\Application\CreatePricingQuote;
use App\Modules\Pricing\Domain\QuoteFailure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreatePricingQuoteController
{
    public function __construct(private CreatePricingQuote $createQuote) {}

    public function __invoke(Request $request): JsonResponse
    {
        $correlationId = (string) Str::uuid();
        $body = $request->getContent();
        if (strlen($body) > 4096) {
            return $this->error('invalid_request', 400, $correlationId);
        }
        try {
            $input = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return $this->error('invalid_request', 400, $correlationId);
        }
        if (! is_array($input) || array_is_list($input) || array_diff(array_keys($input), ['product_slug', 'model_key', 'quantity', 'currency']) !== []
            || ! isset($input['product_slug'], $input['quantity'], $input['currency'])
            || ! is_string($input['product_slug']) || strlen($input['product_slug']) > 180 || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $input['product_slug']) !== 1
            || (array_key_exists('model_key', $input) && (! is_string($input['model_key']) || strlen($input['model_key']) > 120 || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $input['model_key']) !== 1))
            || ! is_string($input['currency'])) {
            return $this->error('invalid_request', 400, $correlationId);
        }
        if (! is_int($input['quantity'])) {
            return $this->error('invalid_quantity', 422, $correlationId);
        }
        try {
            $result = $this->createQuote->execute([
                'product_slug' => $input['product_slug'], 'model_key' => $input['model_key'] ?? null,
                'quantity' => $input['quantity'], 'currency' => $input['currency'],
            ]);
            Log::info('pricing.quote', ['correlation_id' => $correlationId, 'result' => 'quoted']);

            return response()->json($result->toArray())->header('Cache-Control', 'no-store, private');
        } catch (QuoteFailure $failure) {
            Log::info('pricing.quote', ['correlation_id' => $correlationId, 'result' => 'rejected', 'reason' => $failure->publicCode]);

            return $this->error($failure->publicCode, $failure->httpStatus, $correlationId);
        } catch (Throwable $exception) {
            Log::warning('pricing.quote', ['correlation_id' => $correlationId, 'result' => 'failed', 'reason' => 'internal_error']);

            return $this->error('quote_unavailable_temporarily', 503, $correlationId);
        }
    }

    private function error(string $code, int $status, string $correlationId): JsonResponse
    {
        $messages = [
            'invalid_request' => 'A solicitação não é válida.', 'invalid_quantity' => 'Informe uma quantidade dentro dos limites exibidos.',
            'invalid_model' => 'Escolha um modelo disponível.', 'unsupported_currency' => 'A moeda não é aceita.',
            'quote_unavailable' => 'Este produto não está disponível para cotação.', 'rate_limited' => 'Muitas cotações. Tente novamente em instantes.',
            'quote_unavailable_temporarily' => 'Não foi possível calcular o preço agora. Tente novamente.',
        ];

        return response()->json(['error' => ['code' => $code, 'message' => $messages[$code], 'correlation_id' => $correlationId]], $status)
            ->header('Cache-Control', 'no-store, private');
    }
}
