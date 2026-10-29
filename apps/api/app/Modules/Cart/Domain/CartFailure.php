<?php

namespace App\Modules\Cart\Domain;

use RuntimeException;

final class CartFailure extends RuntimeException
{
    public function __construct(public readonly string $publicCode, public readonly int $httpStatus)
    {
        parent::__construct($publicCode);
    }
}
