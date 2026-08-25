<?php

namespace App\Modules\Catalog\Application\Queries;

final readonly class PublicCatalogPage
{
    /**
     * @param  list<array<string, mixed>>  $items
     * @param  array<string, string>  $filterLabels
     */
    public function __construct(
        public array $items,
        public int $currentPage,
        public int $perPage,
        public int $lastPage,
        public int $total,
        public array $filterLabels = [],
    ) {}
}
