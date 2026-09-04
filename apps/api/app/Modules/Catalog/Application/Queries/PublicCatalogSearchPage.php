<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogSearchPage
{
    /**
     * @param  list<PublicCatalogSearchGroup>  $exactGroups
     * @param  list<array<string, mixed>>  $similar
     * @param  list<PublicCatalogSearchSuggestion>  $suggestions
     */
    public function __construct(
        public array $exactGroups,
        public array $similar,
        public array $suggestions,
        public PublicCatalogSearchIntent $intent,
        public string $query,
        public int $currentPage,
        public int $perPage,
        public int $lastPage,
        public int $total,
        public int $totalExact,
        public int $totalSimilar,
    ) {}
}
