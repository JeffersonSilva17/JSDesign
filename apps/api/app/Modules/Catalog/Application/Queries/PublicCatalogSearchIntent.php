<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogSearchIntent
{
    public function __construct(
        public string $type,
        public string $preservedTerm,
        public ?string $handoffHref = null,
    ) {}
}
