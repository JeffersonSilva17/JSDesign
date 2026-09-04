<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogSearchSuggestion
{
    public function __construct(public string $label, public string $href) {}
}
