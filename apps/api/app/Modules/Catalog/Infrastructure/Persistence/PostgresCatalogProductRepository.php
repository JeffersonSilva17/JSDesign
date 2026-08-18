<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\CatalogValidationFailed;
use App\Modules\Catalog\Domain\TaxonomyTerm;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PostgresCatalogProductRepository implements CatalogProductRepository
{
    private const PRODUCT_COLUMNS = [
        'id', 'slug', 'name', 'description', 'modality', 'status', 'category_id', 'price_minor', 'currency',
        'availability', 'delivery_type', 'minimum_quantity', 'variants_reference', 'is_personalized',
        'is_immediate_delivery', 'requires_briefing', 'requires_approval', 'production_lead_time_days',
        'materials', 'composition', 'file_description', 'compatibility', 'usage_terms', 'version',
        'published_at', 'unpublished_at',
    ];

    public function create(array $product): array
    {
        try {
            return DB::transaction(function () use ($product): array {
                $now = now();
                $row = array_intersect_key($product, array_flip(self::PRODUCT_COLUMNS));
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                DB::table('catalog_products')->insert($row);
                $this->syncAssociations((string) $product['id'], $product);

                return $this->find((string) $product['id']) ?? throw new \RuntimeException('Produto persistido não encontrado.');
            });
        } catch (QueryException $exception) {
            $this->rethrowConflict($exception);
        }
    }

    public function update(string $id, int $expectedVersion, array $changes): array
    {
        try {
            return DB::transaction(function () use ($id, $expectedVersion, $changes): array {
                $current = DB::table('catalog_products')->where('id', $id)->lockForUpdate()->first();

                if (! $current) {
                    throw new CatalogConflict('product_not_found', 'Produto não encontrado.');
                }

                if ((int) $current->version !== $expectedVersion) {
                    throw new CatalogConflict('version_conflict', 'A versão informada está desatualizada.');
                }

                $row = array_intersect_key($changes, array_flip(array_diff(self::PRODUCT_COLUMNS, ['id', 'version'])));
                $row['version'] = $expectedVersion + 1;
                $row['updated_at'] = now();

                $updated = DB::table('catalog_products')
                    ->where('id', $id)
                    ->where('version', $expectedVersion)
                    ->update($row);

                if ($updated !== 1) {
                    throw new CatalogConflict('version_conflict', 'O produto foi alterado por outra requisição.');
                }

                $this->syncAssociations($id, $changes);

                return $this->find($id) ?? throw new \RuntimeException('Produto atualizado não encontrado.');
            });
        } catch (QueryException $exception) {
            $this->rethrowConflict($exception);
        }
    }

    public function find(string $id): ?array
    {
        $row = DB::table('catalog_products')->where('id', $id)->first();

        if (! $row) {
            return null;
        }

        $product = (array) $row;
        $this->castProductTypes($product);
        $product['images'] = DB::table('catalog_product_images')
            ->where('product_id', $id)
            ->orderBy('sort_order')
            ->get(['storage_reference', 'alt_text', 'sort_order', 'is_primary'])
            ->map(static fn (object $image): array => (array) $image)
            ->all();
        $associations = DB::table('catalog_product_taxonomy as association')
            ->join('catalog_taxonomy_terms as term', 'term.id', '=', 'association.taxonomy_term_id')
            ->where('association.product_id', $id)
            ->get([
                'term.type', 'term.label', 'term.canonical_key', 'association.is_protected',
                'association.verification_status as status', 'association.verification_notes as notes',
                'association.evidence_reference', 'association.verified_by', 'association.verified_at',
            ])
            ->map(static fn (object $term): array => (array) $term)
            ->all();
        $product['taxonomy'] = array_values(array_filter($associations, static fn (array $term): bool => ! $term['is_protected']));
        $product['protected_assets'] = array_values(array_filter($associations, static fn (array $term): bool => (bool) $term['is_protected']));

        return $product;
    }

    /** @param array<string, mixed> $product */
    private function castProductTypes(array &$product): void
    {
        foreach (['price_minor', 'minimum_quantity', 'production_lead_time_days', 'version'] as $field) {
            if ($product[$field] !== null) {
                $product[$field] = (int) $product[$field];
            }
        }

        foreach (['is_personalized', 'is_immediate_delivery', 'requires_briefing', 'requires_approval'] as $field) {
            if ($product[$field] !== null) {
                $product[$field] = (bool) $product[$field];
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function syncAssociations(string $productId, array $data): void
    {
        if (array_key_exists('taxonomy', $data) || array_key_exists('protected_assets', $data)) {
            DB::table('catalog_product_taxonomy')->where('product_id', $productId)->delete();

            foreach ($data['taxonomy'] ?? [] as $term) {
                $this->attachTerm($productId, $term, false);
            }
            foreach ($data['protected_assets'] ?? [] as $term) {
                $term['type'] = 'character';
                $this->attachTerm($productId, $term, true);
            }
        }

        if (array_key_exists('images', $data)) {
            DB::table('catalog_product_images')->where('product_id', $productId)->delete();
            foreach ($data['images'] as $image) {
                DB::table('catalog_product_images')->insert([
                    'id' => (string) Str::uuid(),
                    'product_id' => $productId,
                    'storage_reference' => $image['storage_reference'],
                    'alt_text' => $image['alt_text'] ?? null,
                    'sort_order' => (int) ($image['sort_order'] ?? 0),
                    'is_primary' => (bool) ($image['is_primary'] ?? false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function attachTerm(string $productId, array $data, bool $protected): void
    {
        $term = TaxonomyTerm::fromLabel((string) $data['label'], (string) $data['type']);
        $existing = DB::table('catalog_taxonomy_terms')
            ->where('type', $term->type)
            ->where('canonical_key', $term->canonicalKey)
            ->first();
        $termId = $existing?->id ?? (string) Str::uuid();

        if (! $existing) {
            DB::table('catalog_taxonomy_terms')->insert([
                'id' => $termId,
                'type' => $term->type,
                'label' => $term->label,
                'canonical_key' => $term->canonicalKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('catalog_product_taxonomy')->insert([
            'product_id' => $productId,
            'taxonomy_term_id' => $termId,
            'is_protected' => $protected,
            'verification_status' => $protected ? ($data['status'] ?? 'pending') : null,
            'verification_notes' => $protected ? ($data['notes'] ?? null) : null,
            'evidence_reference' => $protected ? ($data['evidence_reference'] ?? null) : null,
            'verified_by' => $protected ? ($data['verified_by'] ?? null) : null,
            'verified_at' => $protected ? ($data['verified_at'] ?? null) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function rethrowConflict(QueryException $exception): never
    {
        if ((string) $exception->getCode() === '23505') {
            throw new CatalogConflict('unique_conflict', 'Slug, taxonomia ou associação duplicada.');
        }

        if (in_array((string) $exception->getCode(), ['23503', '23514', '22P02'], true)) {
            throw new CatalogValidationFailed([[
                'code' => 'database_integrity_check_failed',
                'message_key' => 'catalog.validation.database_integrity_check_failed',
                'field_path' => 'product',
                'recoverable' => true,
            ]]);
        }

        throw $exception;
    }
}
