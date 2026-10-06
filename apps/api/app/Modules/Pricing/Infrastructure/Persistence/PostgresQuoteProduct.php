<?php

namespace App\Modules\Pricing\Infrastructure\Persistence;

use App\Modules\Pricing\Application\QuoteProduct;
use Illuminate\Support\Facades\DB;

final readonly class PostgresQuoteProduct implements QuoteProduct
{
    public function findSnapshot(array $input): ?array
    {
        return DB::transaction(function () use ($input): ?array {
            // One PostgreSQL statement gives product, model and pricing data one MVCC snapshot.
            $product = DB::selectOne(<<<'SQL'
                SELECT p.id, p.slug, p.price_minor, p.minimum_quantity, p.modality, p.availability, p.version,
                       r.maximum_quantity, r.version AS rule_version,
                       COALESCE((SELECT json_agg(m.public_key ORDER BY m.sort_order, m.id)
                                 FROM catalog_product_models m WHERE m.product_id = p.id), '[]'::json) AS models,
                       COALESCE((SELECT json_agg(json_build_object(
                                   'minimum_quantity', t.minimum_quantity,
                                   'maximum_quantity', t.maximum_quantity,
                                   'unit_price_minor', t.unit_price_minor)
                                   ORDER BY t.minimum_quantity)
                                 FROM pricing_quantity_tiers t WHERE t.pricing_product_rule_id = r.id), '[]'::json) AS tiers
                FROM catalog_products p
                LEFT JOIN pricing_product_rules r ON r.product_id = p.id
                WHERE p.slug = ? AND p.status = 'published' AND p.published_at <= ?
                  AND (p.unpublished_at IS NULL OR p.unpublished_at > ?)
                  AND p.price_minor IS NOT NULL AND p.currency = 'EUR'
                LIMIT 1
                SQL, [$input['product_slug'], now('UTC'), now('UTC')]);
            if ($product === null) {
                return null;
            }

            $models = json_decode((string) $product->models, true, 16, JSON_THROW_ON_ERROR);
            if ($models === []) {
                $models = ['padrao'];
            }
            $tiers = json_decode((string) $product->tiers, true, 16, JSON_THROW_ON_ERROR);
            $tiers = array_map(static fn (array $tier): array => [
                'minimum_quantity' => (int) $tier['minimum_quantity'],
                'maximum_quantity' => $tier['maximum_quantity'] === null ? null : (int) $tier['maximum_quantity'],
                'unit_price_minor' => (int) $tier['unit_price_minor'],
            ], $tiers);

            return [
                'price_minor' => (int) $product->price_minor,
                'minimum_quantity' => $product->minimum_quantity === null ? null : (int) $product->minimum_quantity,
                'modality' => (string) $product->modality,
                'availability' => (string) $product->availability,
                'version' => max((int) ($product->rule_version ?? 1), (int) $product->version),
                'maximum_quantity' => $product->maximum_quantity === null ? null : (int) $product->maximum_quantity,
                'models' => $models,
                'tiers' => $tiers,
            ];
        }, 1);
    }
}
