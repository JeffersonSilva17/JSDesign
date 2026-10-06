<?php

namespace App\Modules\Pricing\Domain;

use InvalidArgumentException;

final readonly class QuoteQuantity
{
    public function __construct(public int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Cotação exige quantidade inteira positiva.');
        }
    }
}
