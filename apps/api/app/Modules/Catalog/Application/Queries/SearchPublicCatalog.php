<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class SearchPublicCatalog
{
    public function __construct(private PublicCatalogSearchQuery $query) {}

    public function execute(PublicCatalogSearchCriteria $criteria): PublicCatalogSearchPage
    {
        return $this->query->search($criteria);
    }
}
