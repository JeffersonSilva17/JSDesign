<?php

namespace Tests\Unit\Modules\Pricing;

use App\Modules\Pricing\Domain\QuoteCalculator;
use App\Modules\Pricing\Domain\QuoteFailure;
use Tests\TestCase;

final class QuoteCalculatorTest extends TestCase
{
    public function test_applies_inclusive_tier_and_integer_discount_formula(): void
    {
        $quote = (new QuoteCalculator)->calculate('kit', 'padrao', 10, 500, 2, 'physical_personalized', 100, 7, [
            ['minimum_quantity' => 5, 'maximum_quantity' => 10, 'unit_price_minor' => 450],
            ['minimum_quantity' => 11, 'maximum_quantity' => null, 'unit_price_minor' => 400],
        ]);
        self::assertSame(5000, $quote->subtotalMinor);
        self::assertSame(4500, $quote->totalMinor);
        self::assertSame(500, $quote->discountMinor);
        self::assertSame(7, $quote->pricingRuleVersion);
        self::assertSame(10, $quote->appliedTier['maximum_quantity']);
    }

    public function test_quantity_in_tier_gap_uses_base_price(): void
    {
        $quote = (new QuoteCalculator)->calculate('kit', null, 6, 500, 1, 'physical_personalized', 100, 1, [
            ['minimum_quantity' => 2, 'maximum_quantity' => 5, 'unit_price_minor' => 450],
            ['minimum_quantity' => 8, 'maximum_quantity' => null, 'unit_price_minor' => 400],
        ]);
        self::assertSame(500, $quote->unitPriceMinor);
        self::assertNull($quote->appliedTier);
        self::assertSame(0, $quote->discountMinor);
    }

    public function test_digital_ready_accepts_only_one(): void
    {
        $quote = (new QuoteCalculator)->calculate('arquivo', null, 1, 125, null, 'digital_ready', null, 1, []);
        self::assertSame(1, $quote->minimumQuantity);
        self::assertSame(1, $quote->maximumQuantity);
        try {
            (new QuoteCalculator)->calculate('arquivo', null, 2, 125, null, 'digital_ready', null, 1, []);
            self::fail('Invalid digital-ready quantity was accepted.');
        } catch (QuoteFailure $failure) {
            self::assertSame('invalid_quantity', $failure->publicCode);
        }
    }

    public function test_rejects_below_minimum_and_invalid_tier_configuration(): void
    {
        try {
            (new QuoteCalculator)->calculate('kit', null, 1, 100, 2, 'physical_personalized', 10, 1, []);
            self::fail('Below-minimum quantity was accepted.');
        } catch (QuoteFailure $failure) {
            self::assertSame('invalid_quantity', $failure->publicCode);
        }
        try {
            (new QuoteCalculator)->calculate('kit', null, 4, 100, 1, 'physical_personalized', 10, 1, [
                ['minimum_quantity' => 2, 'maximum_quantity' => 5, 'unit_price_minor' => 90],
                ['minimum_quantity' => 5, 'maximum_quantity' => null, 'unit_price_minor' => 80],
            ]);
            self::fail('Overlapping tiers were accepted.');
        } catch (QuoteFailure $failure) {
            self::assertSame('quote_unavailable_temporarily', $failure->publicCode);
        }
    }

    public function test_clamps_effective_maximum_to_ten_thousand(): void
    {
        $quote = (new QuoteCalculator)->calculate('kit', null, 10000, 100, 1, 'physical_personalized', 50000, 1, []);
        self::assertSame(10000, $quote->maximumQuantity);
    }

    public function test_rejects_zero_before_constructing_positive_quantity_value_object(): void
    {
        try {
            (new QuoteCalculator)->calculate('kit', null, 0, 100, 1, 'physical_personalized', 10, 1, []);
            self::fail('Zero quantity was accepted.');
        } catch (QuoteFailure $failure) {
            self::assertSame('invalid_quantity', $failure->publicCode);
        }
    }

    public function test_rejects_integer_overflow_and_invalid_effective_limits_before_multiplication(): void
    {
        $this->expectException(\OverflowException::class);
        (new QuoteCalculator)->calculate('kit', null, 2, PHP_INT_MAX, 1, 'physical_personalized', 2, 1, []);
    }

    public function test_rejects_invalid_effective_limits_before_multiplication(): void
    {
        try {
            (new QuoteCalculator)->calculate('kit', null, 1, 100, 2, 'physical_personalized', 1, 1, []);
            self::fail('Inverted effective limits were accepted.');
        } catch (QuoteFailure $failure) {
            self::assertSame('quote_unavailable_temporarily', $failure->publicCode);
        }
    }
}
