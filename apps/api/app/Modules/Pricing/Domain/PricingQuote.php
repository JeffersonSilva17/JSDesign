<?php

namespace App\Modules\Pricing\Domain;

final readonly class PricingQuote
{
    /** @param array{minimum_quantity:int,maximum_quantity:?int,unit_price_minor:int}|null $appliedTier */
    public function __construct(
        public string $productSlug,
        public ?string $modelKey,
        public int $quantity,
        public int $minimumQuantity,
        public int $maximumQuantity,
        public int $baseUnitPriceMinor,
        public int $unitPriceMinor,
        public int $subtotalMinor,
        public int $discountMinor,
        public int $totalMinor,
        public int $pricingRuleVersion,
        public ?array $appliedTier,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'product_slug' => $this->productSlug, 'model_key' => $this->modelKey,
            'quantity' => $this->quantity, 'minimum_quantity' => $this->minimumQuantity,
            'maximum_quantity' => $this->maximumQuantity, 'base_unit_price_minor' => $this->baseUnitPriceMinor,
            'unit_price_minor' => $this->unitPriceMinor, 'subtotal_minor' => $this->subtotalMinor,
            'discount_minor' => $this->discountMinor, 'total_minor' => $this->totalMinor,
            'currency' => 'EUR', 'pricing_rule_version' => $this->pricingRuleVersion,
            'applied_tier' => $this->appliedTier,
        ];
    }
}
