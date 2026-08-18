<?php

namespace App\Modules\Catalog\Domain;

use RuntimeException;

final class CatalogValidationFailed extends RuntimeException
{
    /** @param list<array{code: string, message_key: string, field_path: string, recoverable: bool, next_action?: string}> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('O produto não atende aos requisitos para esta operação.');
    }
}
