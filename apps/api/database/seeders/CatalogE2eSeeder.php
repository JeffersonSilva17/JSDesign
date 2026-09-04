<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class CatalogE2eSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('O fixture do catálogo só pode ser executado em APP_ENV=testing.');
        }

        $now = now();
        $categoryId = (string) Str::uuid();
        DB::table('catalog_categories')->insert(['id' => $categoryId, 'slug' => 'festas', 'label' => 'Festas', 'created_at' => $now, 'updated_at' => $now]);
        $occasionId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert(['id' => $occasionId, 'type' => 'occasion', 'label' => 'Aniversário', 'canonical_key' => 'aniversario', 'created_at' => $now, 'updated_at' => $now]);
        $themeId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert(['id' => $themeId, 'type' => 'theme', 'label' => 'Convite', 'canonical_key' => 'convite', 'created_at' => $now, 'updated_at' => $now]);
        $productAliasId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert(['id' => $productAliasId, 'type' => 'search_alias', 'label' => 'Produto', 'canonical_key' => 'produto', 'created_at' => $now, 'updated_at' => $now]);
        $verifiedCharacterId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert(['id' => $verifiedCharacterId, 'type' => 'character', 'label' => 'Princesa Aurora', 'canonical_key' => 'princesa-aurora', 'created_at' => $now, 'updated_at' => $now]);
        $pendingCharacterId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert(['id' => $pendingCharacterId, 'type' => 'character', 'label' => 'Personagem Pendente', 'canonical_key' => 'personagem-pendente', 'created_at' => $now, 'updated_at' => $now]);

        for ($index = 1; $index <= 13; $index++) {
            $id = (string) Str::uuid();
            $modality = match ($index % 3) {
                0 => 'physical_personalized', 1 => 'digital_ready', default => 'digital_personalized'
            };
            $name = match ($index) {
                5 => 'Convite',
                6 => 'Modelo festa',
                7 => 'Convite Floral',
                8 => 'Convte',
                default => "Produto $index",
            };
            DB::table('catalog_products')->insert([
                'id' => $id, 'slug' => "produto-$index", 'name' => $name,
                'description' => 'Produto publicado para validar a listagem pública.', 'modality' => $modality,
                'status' => 'published', 'category_id' => $categoryId, 'price_minor' => 1000 + $index,
                'currency' => 'EUR', 'availability' => 'available',
                'delivery_type' => $modality === 'physical_personalized' ? 'physical' : 'digital',
                'is_immediate_delivery' => $modality === 'digital_ready',
                'production_lead_time_days' => $modality === 'digital_ready' ? null : 5,
                'compatibility' => $modality === 'digital_ready' ? 'Silhouette Studio' : null,
                'published_at' => $now->copy()->subMinutes($index), 'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('catalog_product_taxonomy')->insert(['product_id' => $id, 'taxonomy_term_id' => $occasionId, 'is_protected' => false, 'created_at' => $now, 'updated_at' => $now]);
            if ($index >= 5 && $index <= 8) {
                DB::table('catalog_product_taxonomy')->insert(['product_id' => $id, 'taxonomy_term_id' => $productAliasId, 'is_protected' => false, 'created_at' => $now, 'updated_at' => $now]);
            }
            if ($index === 6) {
                DB::table('catalog_product_taxonomy')->insert(['product_id' => $id, 'taxonomy_term_id' => $themeId, 'is_protected' => false, 'created_at' => $now, 'updated_at' => $now]);
            }
            if ($index === 9) {
                DB::table('catalog_product_taxonomy')->insert([
                    'product_id' => $id, 'taxonomy_term_id' => $verifiedCharacterId, 'is_protected' => true,
                    'verification_status' => 'verified', 'evidence_reference' => 'private/e2e-evidence',
                    'verified_by' => 'e2e-admin', 'verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            if ($index === 10) {
                DB::table('catalog_product_taxonomy')->insert([
                    'product_id' => $id, 'taxonomy_term_id' => $pendingCharacterId, 'is_protected' => true,
                    'verification_status' => 'pending', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            if ($index === 1) {
                DB::table('catalog_product_images')->insert(['id' => (string) Str::uuid(), 'product_id' => $id, 'storage_reference' => 'catalog-e2e-image', 'alt_text' => 'Produto digital em tons neutros', 'sort_order' => 0, 'is_primary' => true, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }
}
