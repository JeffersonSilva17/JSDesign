<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AdminCatalogProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'slug' => $this->resource['slug'],
            'name' => $this->resource['name'],
            'description' => $this->resource['description'],
            'modality' => $this->resource['modality'],
            'status' => $this->resource['status'],
            'category_id' => $this->resource['category_id'],
            'price_minor' => $this->resource['price_minor'] === null ? null : (int) $this->resource['price_minor'],
            'currency' => $this->resource['currency'],
            'availability' => $this->resource['availability'],
            'delivery_type' => $this->resource['delivery_type'],
            'minimum_quantity' => $this->resource['minimum_quantity'],
            'variants_reference' => $this->resource['variants_reference'],
            'is_personalized' => $this->resource['is_personalized'],
            'is_immediate_delivery' => $this->resource['is_immediate_delivery'],
            'requires_briefing' => $this->resource['requires_briefing'],
            'requires_approval' => $this->resource['requires_approval'],
            'production_lead_time_days' => $this->resource['production_lead_time_days'],
            'materials' => $this->resource['materials'],
            'composition' => $this->resource['composition'],
            'file_description' => $this->resource['file_description'],
            'compatibility' => $this->resource['compatibility'],
            'usage_terms' => $this->resource['usage_terms'],
            'taxonomy' => $this->resource['taxonomy'] ?? [],
            'images' => $this->resource['images'] ?? [],
            'protected_assets' => $this->resource['protected_assets'] ?? [],
            'version' => (int) $this->resource['version'],
            'published_at' => $this->resource['published_at'],
            'unpublished_at' => $this->resource['unpublished_at'],
            'created_at' => $this->resource['created_at'],
            'updated_at' => $this->resource['updated_at'],
        ];
    }
}
