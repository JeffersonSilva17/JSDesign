<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCatalogProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'slug' => $this->resource['slug'],
            'name' => $this->resource['name'],
            'description' => $this->resource['description'],
            'modality' => $this->resource['modality'],
            'price_minor' => $this->resource['price_minor'],
            'currency' => $this->resource['currency'],
            'availability' => $this->resource['availability'],
            'delivery_type' => $this->resource['delivery_type'],
            'taxonomy' => array_values(array_map(
                static fn (array $term): array => [
                    'type' => $term['type'],
                    'label' => $term['label'],
                    'canonical_key' => $term['canonical_key'],
                ],
                array_filter(
                    $this->resource['taxonomy'] ?? [],
                    static fn (array $term): bool => ($term['type'] ?? null) !== 'character',
                ),
            )),
            'images' => $this->resource['images'] ?? [],
        ];
    }
}
