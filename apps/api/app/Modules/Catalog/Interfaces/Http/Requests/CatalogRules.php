<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Validation\Rule;

final class CatalogRules
{
    public static function product(bool $partial): array
    {
        $presence = $partial ? 'sometimes' : 'required';
        $optional = $partial ? 'sometimes' : 'nullable';

        return [
            'slug' => [$presence, 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'name' => [$optional, 'nullable', 'string', 'max:180'],
            'description' => [$optional, 'nullable', 'string', 'max:10000'],
            'modality' => [$presence, Rule::in(['physical_personalized', 'digital_ready', 'digital_personalized'])],
            'category_id' => [$optional, 'nullable', 'uuid', 'exists:catalog_categories,id'],
            'price_minor' => [$optional, 'nullable', 'integer', 'min:0'],
            'currency' => [$optional, 'nullable', Rule::in(['EUR'])],
            'availability' => [$optional, 'nullable', Rule::in(['available', 'unavailable', 'made_to_order'])],
            'delivery_type' => [$optional, 'nullable', Rule::in(['physical', 'digital'])],
            'minimum_quantity' => [$optional, 'nullable', 'integer', 'min:1'],
            'variants_reference' => [$optional, 'nullable', 'string', 'max:180'],
            'is_personalized' => [$optional, 'nullable', 'boolean'],
            'is_immediate_delivery' => [$optional, 'nullable', 'boolean'],
            'requires_briefing' => [$optional, 'nullable', 'boolean'],
            'requires_approval' => [$optional, 'nullable', 'boolean'],
            'production_lead_time_days' => [$optional, 'nullable', 'integer', 'min:1', 'max:3650'],
            'materials' => [$optional, 'nullable', 'string', 'max:5000'],
            'composition' => [$optional, 'nullable', 'string', 'max:5000'],
            'file_description' => [$optional, 'nullable', 'string', 'max:5000'],
            'compatibility' => [$optional, 'nullable', 'string', 'max:5000'],
            'usage_terms' => [$optional, 'nullable', 'string', 'max:10000'],
            'taxonomy' => [$optional, 'array', 'max:50'],
            'taxonomy.*.type' => ['required', Rule::in(['theme', 'occasion', 'search_alias'])],
            'taxonomy.*.label' => ['required', 'string', 'max:160'],
            'images' => [$optional, 'array', 'max:20'],
            'images.*.storage_reference' => ['required', 'string', 'max:180', 'regex:/^file_[A-Za-z0-9]{20,170}$/'],
            'images.*.alt_text' => ['nullable', 'string', 'max:300'],
            'images.*.sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
            'images.*.is_primary' => ['required', 'boolean'],
            'protected_assets' => [$optional, 'array', 'max:30'],
            'protected_assets.*.label' => ['required', 'string', 'max:160'],
            'protected_assets.*.status' => ['required', Rule::in(['pending', 'verified', 'rejected'])],
            'protected_assets.*.notes' => ['nullable', 'string', 'max:5000'],
            'protected_assets.*.evidence_reference' => ['nullable', 'string', 'max:180', 'regex:/^evidence_[A-Za-z0-9]{12,170}$/'],
            'protected_assets.*.verified_by' => ['prohibited'],
            'protected_assets.*.verified_at' => ['prohibited'],
        ];
    }
}
