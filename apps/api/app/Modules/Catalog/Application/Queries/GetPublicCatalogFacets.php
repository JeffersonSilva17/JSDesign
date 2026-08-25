<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class GetPublicCatalogFacets
{
    public function __construct(private PublicCatalogQuery $query) {}

    public function execute(): array
    {
        return $this->query->facets();
    }
}
