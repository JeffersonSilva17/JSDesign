<?php

namespace App\Modules\Catalog\Domain;

use RuntimeException;

final class CatalogConflict extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message)
    {
        parent::__construct($message);
    }
}
