<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Infrastructure\Persistence\PostgresCatalogProductRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CatalogPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_persists_structured_product_and_associations_transactionally(): void
    {
        $categoryId = $this->category();
        $repository = app(PostgresCatalogProductRepository::class);

        $created = $repository->create($this->product($categoryId));

        self::assertSame('convite-fazendinha', $created['slug']);
        self::assertSame(1, DB::table('catalog_products')->count());
        self::assertSame(2, DB::table('catalog_taxonomy_terms')->count());
        self::assertSame(2, DB::table('catalog_product_taxonomy')->count());
        self::assertSame(1, DB::table('catalog_product_images')->count());
    }

    public function test_database_rejects_duplicate_slug(): void
    {
        $categoryId = $this->category();
        $repository = app(PostgresCatalogProductRepository::class);
        $repository->create($this->product($categoryId));

        $this->expectException(CatalogConflict::class);
        $repository->create($this->product($categoryId, ['id' => (string) Str::uuid()]));
    }

    public function test_database_rejects_duplicate_canonical_taxonomy_for_same_type(): void
    {
        DB::table('catalog_taxonomy_terms')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'theme',
            'label' => 'Coração',
            'canonical_key' => 'coracao',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);
        DB::table('catalog_taxonomy_terms')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'theme',
            'label' => 'CORAÇÃO',
            'canonical_key' => 'coracao',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_postgresql_allows_only_one_primary_image_per_product(): void
    {
        $categoryId = $this->category();
        $repository = app(PostgresCatalogProductRepository::class);
        $product = $repository->create($this->product($categoryId));

        $this->expectException(QueryException::class);
        DB::table('catalog_product_images')->insert([
            'id' => (string) Str::uuid(),
            'product_id' => $product['id'],
            'storage_reference' => 'file_01HSECONDPRIMARY0000000000',
            'alt_text' => 'Segunda principal',
            'sort_order' => 1,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function category(): string
    {
        $id = (string) Str::uuid();
        DB::table('catalog_categories')->insert([
            'id' => $id,
            'slug' => 'convites',
            'label' => 'Convites',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    /** @param array<string, mixed> $overrides */
    private function product(string $categoryId, array $overrides = []): array
    {
        return array_replace([
            'id' => (string) Str::uuid(),
            'slug' => 'convite-fazendinha',
            'name' => 'Convite Fazendinha',
            'description' => 'Produto de teste',
            'modality' => 'digital_ready',
            'status' => 'draft',
            'category_id' => $categoryId,
            'price_minor' => 1299,
            'currency' => 'EUR',
            'availability' => 'available',
            'delivery_type' => 'digital',
            'is_personalized' => false,
            'is_immediate_delivery' => true,
            'requires_briefing' => false,
            'requires_approval' => false,
            'file_description' => 'PDF',
            'compatibility' => 'Leitor PDF',
            'usage_terms' => 'Uso pessoal',
            'version' => 1,
            'taxonomy' => [
                ['type' => 'theme', 'label' => 'Fazendinha'],
                ['type' => 'search_alias', 'label' => 'Animais'],
            ],
            'images' => [[
                'storage_reference' => 'file_01HXYZABCDEF0123456789ABCD',
                'alt_text' => 'Animais da fazenda',
                'sort_order' => 0,
                'is_primary' => true,
            ]],
            'protected_assets' => [],
        ], $overrides);
    }
}
