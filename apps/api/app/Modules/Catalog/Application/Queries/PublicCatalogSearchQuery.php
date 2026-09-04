<?php

namespace App\Modules\Catalog\Application\Queries;

interface PublicCatalogSearchQuery
{
    public function search(PublicCatalogSearchCriteria $criteria): PublicCatalogSearchPage;
}
