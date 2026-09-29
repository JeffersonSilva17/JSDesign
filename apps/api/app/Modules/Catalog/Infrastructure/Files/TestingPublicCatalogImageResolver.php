<?php

namespace App\Modules\Catalog\Infrastructure\Files;

use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;

final class TestingPublicCatalogImageResolver implements PublicCatalogImageResolver
{
    public function resolveBatch(array $references): array
    {
        if (! app()->environment('testing')) {
            return [];
        }

        return array_intersect_key([
            'catalog-e2e-image' => '/catalog-e2e-product.svg',
            'catalog-e2e-detail' => '/catalog-e2e-product.svg',
        ], array_flip($references));
    }
}
