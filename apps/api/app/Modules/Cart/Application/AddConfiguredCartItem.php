<?php

namespace App\Modules\Cart\Application;

use App\Modules\Cart\Domain\CartConfiguration;
use App\Modules\Cart\Domain\CartFailure;
use App\Modules\Pricing\Application\CreatePricingQuote;
use App\Modules\Pricing\Domain\QuoteFailure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class AddConfiguredCartItem
{
    public function __construct(private CreatePricingQuote $quotes) {}

    /** @param array<string,mixed> $input @return array{result:array<string,mixed>,replay:bool} */
    public function execute(array $input, string $ownerHash, string $idempotencyKey): array
    {
        $payloadHash = CanonicalJson::hash($input);

        return DB::transaction(function () use ($input, $ownerHash, $idempotencyKey, $payloadHash): array {
            DB::select('SELECT pg_advisory_xact_lock(hashtextextended(?, 0))', [$ownerHash]);
            $existing = DB::table('cart_idempotency_keys')->where('owner_hash', $ownerHash)->where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
            if ($existing !== null) {
                if (now()->greaterThanOrEqualTo($existing->expires_at)) {
                    DB::table('cart_idempotency_keys')->where('id', $existing->id)->delete();
                    $existing = null;
                }
            }
            if ($existing !== null) {
                if (! hash_equals((string) $existing->payload_hash, $payloadHash)) {
                    throw new CartFailure('idempotency_conflict', 409);
                }
                $item = DB::table('cart_items')->where('id', $existing->cart_item_id)->first();
                if ($item === null) {
                    throw new CartFailure('temporarily_unavailable', 503);
                }

                return ['result' => $this->result($item), 'replay' => true];
            }

            $product = DB::table('catalog_products')->where('slug', $input['product_slug'])->lockForUpdate()->first();
            if ($product === null || $product->status !== 'published' || $product->availability === 'unavailable'
                || $product->published_at === null || now()->lt($product->published_at)
                || ($product->unpublished_at !== null && now()->gte($product->unpublished_at))) {
                throw new CartFailure('unavailable', 409);
            }
            if (! in_array($product->modality, ['physical_personalized', 'digital_personalized'], true)) {
                throw new CartFailure('unsupported_mode', 422);
            }
            $model = DB::table('catalog_product_models')->where('product_id', $product->id)->where('public_key', $input['model_key'])->lockForUpdate()->first();
            if ($model === null) {
                throw new CartFailure('unavailable', 409);
            }
            $pricingRule = DB::table('pricing_product_rules')->where('product_id', $product->id)->lockForUpdate()->first();
            if ($pricingRule === null) {
                throw new CartFailure('unavailable', 409);
            }
            DB::table('pricing_quantity_tiers')->where('pricing_product_rule_id', $pricingRule->id)->orderBy('minimum_quantity')->lockForUpdate()->get(['id']);
            $config = CartConfiguration::validate($input['configuration'], (string) $product->modality, (bool) ($product->allows_deferred_briefing ?? false));
            if ($product->modality === 'digital_personalized') {
                $expectedType = in_array($model->public_key, ['standard', 'complex'], true) ? $model->public_key : null;
                if ($expectedType === null || $config->value['invite_type'] !== $expectedType) {
                    throw new CartFailure('invalid_configuration', 422);
                }
            }
            try {
                $quote = $this->quotes->execute([
                    'product_slug' => $product->slug, 'model_key' => $model->public_key,
                    'quantity' => $input['quantity'], 'currency' => 'EUR',
                ])->toArray();
            } catch (QuoteFailure $failure) {
                throw new CartFailure(in_array($failure->publicCode, ['quote_unavailable', 'invalid_model'], true) ? 'unavailable' : 'invalid_configuration', $failure->httpStatus === 404 ? 409 : $failure->httpStatus);
            }
            if (! hash_equals((string) $quote['quote_snapshot_id'], $input['quote_snapshot_id'])) {
                throw new CartFailure('stale_quote', 409);
            }

            $cart = DB::table('guest_carts')->where('owner_hash', $ownerHash)->lockForUpdate()->first();
            if ($cart !== null && now()->greaterThanOrEqualTo($cart->expires_at)) {
                DB::table('guest_carts')->where('id', $cart->id)->delete();
                $cart = null;
            }
            if ($cart === null) {
                $cartId = (string) Str::uuid();
                DB::table('guest_carts')->insert(['id' => $cartId, 'owner_hash' => $ownerHash, 'expires_at' => now()->addDays(30), 'created_at' => now(), 'updated_at' => now()]);
            } else {
                $cartId = (string) $cart->id;
                DB::table('guest_carts')->where('id', $cartId)->update(['expires_at' => now()->addDays(30), 'updated_at' => now()]);
            }
            if (DB::table('cart_items')->where('cart_id', $cartId)->count() >= max(1, (int) config('cart.max_items', 50))) {
                throw new CartFailure('unavailable', 409);
            }
            $itemId = (string) Str::uuid();
            DB::table('cart_items')->insert([
                'id' => $itemId, 'cart_id' => $cartId, 'product_id' => $product->id, 'model_id' => $model->id,
                'quantity' => $input['quantity'], 'configuration' => json_encode($config->value, JSON_THROW_ON_ERROR),
                'quote_snapshot_id' => $quote['quote_snapshot_id'], 'total_minor' => $quote['total_minor'], 'currency' => 'EUR',
                'pricing_rule_version' => $quote['pricing_rule_version'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('cart_idempotency_keys')->insert([
                'owner_hash' => $ownerHash, 'idempotency_key' => $idempotencyKey, 'payload_hash' => $payloadHash,
                'cart_item_id' => $itemId, 'expires_at' => now()->addHours(24), 'created_at' => now(), 'updated_at' => now(),
            ]);
            $item = DB::table('cart_items')->where('id', $itemId)->first();

            return ['result' => $this->result($item), 'replay' => false];
        }, 3);
    }

    /** @return array<string,mixed> */
    private function result(object $item): array
    {
        $product = DB::table('catalog_products')->where('id', $item->product_id)->first();
        $model = DB::table('catalog_product_models')->where('id', $item->model_id)->first();
        $configuration = is_array($item->configuration) ? $item->configuration : json_decode((string) $item->configuration, true, 16, JSON_THROW_ON_ERROR);

        return [
            'item_reference' => (string) $item->id, 'product_id' => (string) $item->product_id,
            'product_slug' => (string) $product->slug, 'model_id' => (string) $item->model_id,
            'model_key' => (string) $model->public_key, 'quantity' => (int) $item->quantity,
            'configuration' => $configuration, 'quote_snapshot_id' => (string) $item->quote_snapshot_id,
            'quote' => ['total_minor' => (int) $item->total_minor, 'currency' => 'EUR', 'pricing_rule_version' => (int) $item->pricing_rule_version],
            'created_at' => (string) $item->created_at,
        ];
    }
}
