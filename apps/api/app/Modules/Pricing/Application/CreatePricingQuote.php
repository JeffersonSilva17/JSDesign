<?php

namespace App\Modules\Pricing\Application;

use App\Modules\Pricing\Domain\PricingQuote;
use App\Modules\Pricing\Domain\QuoteCalculator;
use App\Modules\Pricing\Domain\QuoteCurrency;
use App\Modules\Pricing\Domain\QuoteFailure;
use OverflowException;

final readonly class CreatePricingQuote
{
    public function __construct(private QuoteProduct $products, private QuoteCalculator $calculator) {}

    /** @param array{product_slug:string,model_key?:string,quantity:int,currency:string} $input */
    public function execute(array $input): PricingQuote
    {
        if (QuoteCurrency::tryFrom($input['currency']) !== QuoteCurrency::EUR) {
            throw new QuoteFailure('unsupported_currency', 422);
        }
        $snapshot = $this->products->findSnapshot($input);
        if ($snapshot === null) {
            throw new QuoteFailure('quote_unavailable', 404);
        }
        if ($snapshot['availability'] === 'unavailable') {
            throw new QuoteFailure('quote_unavailable', 404);
        }
        if ($input['model_key'] !== null && ! in_array($input['model_key'], $snapshot['models'], true)) {
            throw new QuoteFailure('invalid_model', 422);
        }
        try {
            return $this->calculator->calculate(
                $input['product_slug'], $input['model_key'], $input['quantity'], $snapshot['price_minor'],
                $snapshot['minimum_quantity'], $snapshot['modality'], $snapshot['maximum_quantity'],
                $snapshot['version'], $snapshot['tiers'],
            );
        } catch (OverflowException) {
            throw new QuoteFailure('invalid_quantity', 422);
        }
    }
}
