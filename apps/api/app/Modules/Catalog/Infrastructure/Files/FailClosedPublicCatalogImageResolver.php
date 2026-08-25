<?php

namespace App\Modules\Catalog\Infrastructure\Files;

use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;

final class FailClosedPublicCatalogImageResolver implements PublicCatalogImageResolver
{
    public function resolveBatch(array $references): array
    {
        return [];
    }
}
