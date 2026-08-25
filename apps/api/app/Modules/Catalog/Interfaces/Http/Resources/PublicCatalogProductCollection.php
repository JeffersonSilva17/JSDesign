<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class PublicCatalogProductCollection extends ResourceCollection
{
    public function __construct(private readonly PublicCatalogPage $page, private readonly PublicCatalogFilters $filters)
    {
        parent::__construct($page->items);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => PublicCatalogProductResource::collection($this->collection)->resolve($request),
            'meta' => [
                'current_page' => $this->page->currentPage, 'per_page' => $this->page->perPage,
                'last_page' => $this->page->lastPage, 'total' => $this->page->total,
                'applied_filters' => (object) $this->filters->applied(), 'filter_labels' => (object) $this->page->filterLabels,
            ],
        ];
    }
}
