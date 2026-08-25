<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCatalogProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $base = [
            'id' => $this->resource['id'],
            'slug' => $this->resource['slug'],
            'name' => $this->resource['name'],
            'category' => $this->resource['category'],
            'modality' => $this->resource['modality'],
            'price_minor' => $this->resource['price_minor'],
            'currency' => $this->resource['currency'],
            'availability' => $this->resource['availability'],
            'delivery_type' => $this->resource['delivery_type'],
            'production_lead_time_days' => $this->resource['production_lead_time_days'],
            'is_immediate_delivery' => $this->resource['is_immediate_delivery'],
            'primary_image' => $this->resource['primary_image'],
            'taxonomy' => $this->publicTaxonomy($this->resource['taxonomy'] ?? []),
        ];

        if (array_key_exists('description_excerpt', $this->resource)) {
            return [
                'id' => $base['id'],
                'slug' => $base['slug'],
                'name' => $base['name'],
                'description_excerpt' => $this->resource['description_excerpt'],
                'category' => $base['category'],
                'modality' => $base['modality'],
                'price_minor' => $base['price_minor'],
                'currency' => $base['currency'],
                'availability' => $base['availability'],
                'delivery_type' => $base['delivery_type'],
                'production_lead_time_days' => $base['production_lead_time_days'],
                'is_immediate_delivery' => $base['is_immediate_delivery'],
                'primary_image' => $base['primary_image'],
                'taxonomy' => $base['taxonomy'],
                'compatibility_excerpt' => $this->resource['compatibility_excerpt'],
            ];
        }

        return [
            'id' => $base['id'],
            'slug' => $base['slug'],
            'name' => $base['name'],
            'description' => $this->resource['description'],
            'category' => $base['category'],
            'modality' => $base['modality'],
            'price_minor' => $base['price_minor'],
            'currency' => $base['currency'],
            'availability' => $base['availability'],
            'delivery_type' => $base['delivery_type'],
            'production_lead_time_days' => $base['production_lead_time_days'],
            'is_immediate_delivery' => $base['is_immediate_delivery'],
            'primary_image' => $base['primary_image'],
            'taxonomy' => $base['taxonomy'],
            'compatibility' => $this->resource['compatibility'],
        ];
    }

    /**
     * @return list<array{type: string, key: string, label: string}>
     */
    private function publicTaxonomy(mixed $taxonomy): array
    {
        if (! is_array($taxonomy)) {
            return [];
        }

        $public = [];
        foreach ($taxonomy as $term) {
            if (! is_array($term) || ! in_array($term['type'] ?? null, ['theme', 'occasion'], true)) {
                continue;
            }

            $key = (string) ($term['key'] ?? $term['canonical_key'] ?? '');
            $label = (string) ($term['label'] ?? '');
            if ($key === '' || trim($label) === '') {
                continue;
            }

            $public[$term['type'].'|'.$key] = [
                'type' => (string) $term['type'],
                'key' => $key,
                'label' => $label,
            ];
        }

        return array_values($public);
    }
}
