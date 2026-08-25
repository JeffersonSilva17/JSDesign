<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class ListPublicCatalogProducts
{
    public function __construct(private PublicCatalogQuery $query) {}

    public function execute(PublicCatalogFilters $filters): PublicCatalogPage
    {
        return $this->query->list($filters);
    }
}
