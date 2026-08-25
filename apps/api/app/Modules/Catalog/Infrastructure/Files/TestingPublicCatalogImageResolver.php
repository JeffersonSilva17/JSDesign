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

        return in_array('catalog-e2e-image', $references, true)
            ? ['catalog-e2e-image' => '/catalog-e2e-product.svg']
            : [];
    }
}
