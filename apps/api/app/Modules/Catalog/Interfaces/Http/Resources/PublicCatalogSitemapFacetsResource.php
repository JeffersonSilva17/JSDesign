<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCatalogSitemapFacetsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['data' => ['categories' => array_map(static fn (array $category): array => ['slug' => $category['slug'], 'label' => $category['label']], $this->resource)]];
    }
}
