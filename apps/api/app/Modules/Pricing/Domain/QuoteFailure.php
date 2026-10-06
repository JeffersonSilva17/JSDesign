<?php

namespace App\Modules\Pricing\Domain;

use RuntimeException;

final class QuoteFailure extends RuntimeException
{
    public function __construct(public readonly string $publicCode, public readonly int $httpStatus)
    {
        parent::__construct($publicCode);
    }
}
