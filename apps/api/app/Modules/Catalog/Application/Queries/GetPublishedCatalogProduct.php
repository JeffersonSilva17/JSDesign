<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class GetPublishedCatalogProduct
{
    public function __construct(private PublicCatalogQuery $query) {}

    public function execute(string $slug): ?array
    {
        return $this->query->findPublishedBySlug($slug);
    }
}
