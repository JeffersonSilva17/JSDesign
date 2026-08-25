<?php

namespace App\Modules\Catalog\Application\Queries;

interface PublicCatalogImageResolver
{
    /**
     * @param  list<string>  $references
     * @return array<string, string> Map keyed by opaque reference, containing only validated same-origin paths.
     */
    public function resolveBatch(array $references): array;
}
