<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogSearchGroup
{
    /** @param list<array<string, mixed>> $items */
    public function __construct(
        public string $slug,
        public string $label,
        public array $items,
    ) {}
}
