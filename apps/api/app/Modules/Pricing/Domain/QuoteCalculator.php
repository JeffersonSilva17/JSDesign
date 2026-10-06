<?php

namespace App\Modules\Pricing\Domain;

use App\Modules\Catalog\Domain\Money;
use OverflowException;

final class QuoteCalculator
{
    /** @param list<array{minimum_quantity:int,maximum_quantity:?int,unit_price_minor:int}> $tiers */
    public function calculate(
        string $slug,
        ?string $modelKey,
        int $quantity,
        int $basePriceMinor,
        ?int $publicMinimum,
        string $modality,
        ?int $configuredMaximum,
        int $version,
        array $tiers,
    ): PricingQuote {
        if ($quantity < 1) {
            throw new QuoteFailure('invalid_quantity', 422);
        }
        $requestedQuantity = new QuoteQuantity($quantity);
        $basePrice = new Money($basePriceMinor, QuoteCurrency::EUR->value);
        $minimum = $modality === 'digital_ready' ? 1 : max(1, $publicMinimum ?? 1);
        $maximum = $modality === 'digital_ready' ? 1 : min(10000, $configuredMaximum ?? 10000);
        if ($maximum < $minimum || $basePriceMinor < 0 || $version < 1) {
            throw new QuoteFailure('quote_unavailable_temporarily', 503);
        }
        if ($requestedQuantity->value < $minimum || $requestedQuantity->value > $maximum || ($modality === 'digital_ready' && $requestedQuantity->value !== 1)) {
            throw new QuoteFailure('invalid_quantity', 422);
        }
        if ($basePrice->minor > 0 && $requestedQuantity->value > intdiv(PHP_INT_MAX, $basePrice->minor)) {
            throw new OverflowException('Quote multiplication overflow.');
        }
        $subtotal = $basePrice->minor * $requestedQuantity->value;
        $previousMinimum = 0;
        $previousMaximum = 0;
        $previousPrice = PHP_INT_MAX;
        $selected = null;
        foreach ($tiers as $tier) {
            $min = $tier['minimum_quantity'];
            $max = $tier['maximum_quantity'];
            $price = $tier['unit_price_minor'];
            if ($min <= $previousMinimum || ($max !== null && $max < $min) || ($previousMaximum !== 0 && $min <= $previousMaximum)
                || $price < 0 || $price > $basePriceMinor || $price > $previousPrice) {
                throw new QuoteFailure('quote_unavailable_temporarily', 503);
            }
            $selected = $quantity >= $min && ($max === null || $quantity <= $max) ? $tier : $selected;
            $previousMinimum = $min;
            $previousMaximum = $max ?? PHP_INT_MAX;
            $previousPrice = $price;
        }
        $unitPrice = new Money($selected['unit_price_minor'] ?? $basePrice->minor, QuoteCurrency::EUR->value);
        if ($unitPrice->minor > 0 && $requestedQuantity->value > intdiv(PHP_INT_MAX, $unitPrice->minor)) {
            throw new OverflowException('Quote multiplication overflow.');
        }
        $total = $unitPrice->minor * $requestedQuantity->value;
        $discount = $subtotal - $total;

        return new PricingQuote($slug, $modelKey, $requestedQuantity->value, $minimum, $maximum, $basePrice->minor, $unitPrice->minor,
            $subtotal, $discount, $total, $version, $selected);
    }
}
