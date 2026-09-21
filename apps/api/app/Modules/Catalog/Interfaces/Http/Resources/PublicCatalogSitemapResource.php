<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use App\Modules\Catalog\Application\Queries\PublicCatalogSitemapPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCatalogSitemapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var PublicCatalogSitemapPage $page */
        $page = $this->resource;

        return ['data' => array_map(static fn (string $slug): array => ['slug' => $slug], $page->slugs), 'meta' => ['current_page' => $page->currentPage, 'per_page' => 500, 'last_page' => $page->lastPage(), 'total' => $page->total]];
    }
}
