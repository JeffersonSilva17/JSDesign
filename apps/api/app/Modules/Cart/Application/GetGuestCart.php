<?php

namespace App\Modules\Cart\Application;

use Illuminate\Support\Facades\DB;

final class GetGuestCart
{
    /** @return list<array<string,mixed>> */
    public function execute(string $ownerHash): array
    {
        $cart = DB::table('guest_carts')->where('owner_hash', $ownerHash)->where('expires_at', '>', now())->first();
        if ($cart === null) {
            return [];
        }
        $rows = DB::table('cart_items as item')->join('catalog_products as product', 'product.id', '=', 'item.product_id')
            ->join('catalog_product_models as model', 'model.id', '=', 'item.model_id')
            ->where('item.cart_id', $cart->id)->orderBy('item.created_at')->limit(max(1, (int) config('cart.max_items', 50)))->get([
                'item.id as item_reference', 'item.product_id', 'product.slug as product_slug', 'item.model_id',
                'model.public_key as model_key', 'item.quantity', 'item.configuration', 'item.quote_snapshot_id',
                'item.total_minor', 'item.pricing_rule_version', 'item.created_at',
            ]);

        return $rows->map(static function (object $item): array {
            return [
                'item_reference' => (string) $item->item_reference, 'product_id' => (string) $item->product_id,
                'product_slug' => (string) $item->product_slug, 'model_id' => (string) $item->model_id,
                'model_key' => (string) $item->model_key, 'quantity' => (int) $item->quantity,
                'configuration' => is_array($item->configuration) ? $item->configuration : json_decode((string) $item->configuration, true, 16, JSON_THROW_ON_ERROR),
                'quote_snapshot_id' => (string) $item->quote_snapshot_id,
                'quote' => ['total_minor' => (int) $item->total_minor, 'currency' => 'EUR', 'pricing_rule_version' => (int) $item->pricing_rule_version],
                'created_at' => (string) $item->created_at,
            ];
        })->all();
    }
}
