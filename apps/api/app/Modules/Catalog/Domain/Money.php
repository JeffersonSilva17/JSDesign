<?php

namespace App\Modules\Catalog\Domain;

use InvalidArgumentException;

final readonly class Money
{
    public string $currency;

    public function __construct(public int $minor, string $currency)
    {
        if ($minor < 0) {
            throw new InvalidArgumentException('O valor monetário não pode ser negativo.');
        }

        $currency = strtoupper(trim($currency));

        if ($currency !== 'EUR') {
            throw new InvalidArgumentException('A moeda suportada nesta versão do catálogo é EUR (ISO 4217).');
        }

        $this->currency = $currency;
    }
}
