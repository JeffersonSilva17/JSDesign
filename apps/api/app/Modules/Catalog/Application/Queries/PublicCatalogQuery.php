<?php

namespace App\Modules\Catalog\Application\Queries;

interface PublicCatalogQuery
{
    public function list(PublicCatalogFilters $filters): PublicCatalogPage;

    /** @return array{categories: list<array{slug: string, label: string}>, occasions: list<array{key: string, label: string}>, modalities: list<array{value: string, label: string}>} */
    public function facets(): array;

    /** @return array<string, mixed>|null */
    public function findPublishedBySlug(string $slug): ?array;
}
