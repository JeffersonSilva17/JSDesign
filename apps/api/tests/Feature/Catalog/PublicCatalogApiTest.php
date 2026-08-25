<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;
use App\Modules\Catalog\Application\Queries\PublicCatalogPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

final class PublicCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear(md5('public-catalogpublic-catalog:'.hash('sha256', '127.0.0.1')));
    }

    public function test_listing_exposes_only_published_products_with_an_exact_allowlist(): void
    {
        $category = $this->category('festas', 'Festas');
        $published = $this->product($category, 'convite-digital', 'Convite digital', 'published', 'digital_ready');
        $this->product($category, 'rascunho', 'Rascunho', 'draft', 'digital_ready');
        $this->taxonomy($published, 'occasion', 'Aniversário', 'aniversario');
        $this->taxonomy($published, 'character', 'Personagem protegido', 'personagem-protegido', true);
        $this->image($published, 'opaque/private.jpg');

        $response = $this->getJson('/api/v1/catalog/products?category=festas');

        $response->assertOk()->assertHeader('Cache-Control', 'no-store, private')
            ->assertExactJson([
                'data' => [[
                    'id' => $published,
                    'slug' => 'convite-digital',
                    'name' => 'Convite digital',
                    'description_excerpt' => 'Descrição pública do produto',
                    'category' => ['slug' => 'festas', 'label' => 'Festas'],
                    'modality' => 'digital_ready',
                    'price_minor' => 1290,
                    'currency' => 'EUR',
                    'availability' => 'available',
                    'delivery_type' => 'digital',
                    'production_lead_time_days' => null,
                    'is_immediate_delivery' => true,
                    'compatibility_excerpt' => 'Silhouette Studio',
                    'primary_image' => null,
                    'taxonomy' => [[
                        'type' => 'occasion',
                        'key' => 'aniversario',
                        'label' => 'Aniversário',
                    ]],
                ]],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => 12,
                    'last_page' => 1,
                    'total' => 1,
                    'applied_filters' => ['category' => 'festas'],
                    'filter_labels' => ['category' => 'Festas'],
                ],
            ]);

        self::assertStringNotContainsString('storage_reference', $response->getContent());
        self::assertStringNotContainsString('character', $response->getContent());
        self::assertStringNotContainsString('status', $response->getContent());
    }

    public function test_filters_combine_by_and_and_facets_ignore_unpublished_products(): void
    {
        $festas = $this->category('festas', 'Festas');
        $papelaria = $this->category('papelaria', 'Papelaria');
        $wanted = $this->product($festas, 'topo-bolo', 'Topo de bolo', 'published', 'physical_personalized');
        $other = $this->product($papelaria, 'agenda', 'Agenda', 'published', 'physical_personalized');
        $draft = $this->product($papelaria, 'rascunho', 'Rascunho', 'draft', 'digital_ready');
        $this->taxonomy($wanted, 'occasion', 'Aniversário', 'aniversario');
        $this->taxonomy($other, 'occasion', 'Casamento', 'casamento');
        $this->taxonomy($draft, 'occasion', 'Natal', 'natal');

        $this->getJson('/api/v1/catalog/products?category=festas&occasion=aniversario&modality=physical_personalized')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'topo-bolo');

        $this->getJson('/api/v1/catalog/products?category=festas&occasion=casamento')
            ->assertOk()->assertJsonCount(0, 'data')->assertJsonPath('meta.total', 0)
            ->assertJsonPath('meta.current_page', 1);

        $this->getJson('/api/v1/catalog/facets')->assertOk()->assertJsonMissing(['key' => 'natal'])
            ->assertJsonCount(2, 'data.categories')->assertJsonCount(2, 'data.occasions')
            ->assertJsonCount(1, 'data.modalities');
    }

    public function test_detail_returns_only_published_products(): void
    {
        $category = $this->category('digitais', 'Digitais');
        $this->product($category, 'publicado', 'Publicado', 'published', 'digital_ready');
        $this->product($category, 'privado', 'Privado', 'draft', 'digital_ready');

        $this->getJson('/api/v1/catalog/products/publicado')->assertOk()
            ->assertJsonPath('data.description', 'Descrição pública do produto');
        $this->getJson('/api/v1/catalog/products/privado')->assertNotFound()
            ->assertExactJson(['message' => 'Produto não encontrado.']);
        $this->getJson('/api/v1/catalog/products/../segredo')->assertNotFound();
    }

    public function test_query_validation_rejects_unknown_duplicate_array_and_non_canonical_values(): void
    {
        $this->getJson('/api/v1/catalog/products?character=x')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category=festas&category=outra')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category[]=festas')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category%5B%5D=festas')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category]=festas')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category=festas;modality=digital_ready')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category=%E0%A4%A')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?page=01')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?page=10001')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?per_page=49')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/products?category=festas')->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_pagination_is_stable_and_preserves_out_of_range_semantics(): void
    {
        $category = $this->category('festas', 'Festas');
        $ids = [];
        for ($index = 1; $index <= 13; $index++) {
            $ids[] = $this->product($category, "produto-$index", "Produto $index", 'published', 'digital_ready');
        }
        DB::table('catalog_products')->update(['published_at' => now()]);
        rsort($ids, SORT_STRING);

        $first = $this->getJson('/api/v1/catalog/products?page=1&per_page=12')->assertOk();
        $second = $this->getJson('/api/v1/catalog/products?page=2&per_page=12')->assertOk();
        self::assertSame(array_slice($ids, 0, 12), array_column($first->json('data'), 'id'));
        self::assertSame(array_slice($ids, 12), array_column($second->json('data'), 'id'));

        $this->getJson('/api/v1/catalog/products?page=99')->assertOk()->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.current_page', 99)->assertJsonPath('meta.last_page', 2);
        DB::table('catalog_products')->delete();
        $this->getJson('/api/v1/catalog/products?page=99')->assertOk()->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 1)->assertJsonPath('meta.total', 0);
    }

    public function test_image_resolver_output_is_revalidated_before_publication(): void
    {
        $this->app->instance(PublicCatalogImageResolver::class, new class implements PublicCatalogImageResolver
        {
            public function resolveBatch(array $references): array
            {
                return array_fill_keys($references, 'https://unsafe.example/image.jpg');
            }
        });
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'produto', 'Produto', 'published', 'digital_ready');
        $this->image($product, 'opaque-reference');

        $this->getJson('/api/v1/catalog/products')->assertOk()->assertJsonPath('data.0.primary_image', null);
    }

    public function test_public_resource_defends_allowlist_even_with_alternate_query_adapter(): void
    {
        $this->app->instance(PublicCatalogQuery::class, new class implements PublicCatalogQuery
        {
            public function list(PublicCatalogFilters $filters): PublicCatalogPage
            {
                return new PublicCatalogPage([[
                    'id' => '10d6c281-33b2-4d97-857e-29ffccec8ae9',
                    'slug' => 'produto-publico',
                    'name' => 'Produto público',
                    'description_excerpt' => 'Descrição pública',
                    'category' => ['slug' => 'festas', 'label' => 'Festas'],
                    'modality' => 'digital_ready',
                    'price_minor' => 1290,
                    'currency' => 'EUR',
                    'availability' => 'available',
                    'delivery_type' => 'digital',
                    'production_lead_time_days' => null,
                    'is_immediate_delivery' => true,
                    'primary_image' => null,
                    'taxonomy' => [
                        ['type' => 'occasion', 'key' => 'aniversario', 'label' => 'Aniversário'],
                        ['type' => 'character', 'key' => 'personagem', 'label' => 'Personagem'],
                        ['type' => 'search_alias', 'key' => 'apelido', 'label' => 'Apelido'],
                    ],
                    'compatibility_excerpt' => null,
                ]], 1, 12, 1, 1);
            }

            public function facets(): array
            {
                return [];
            }

            public function findPublishedBySlug(string $slug): ?array
            {
                return null;
            }
        });

        $response = $this->getJson('/api/v1/catalog/products')->assertOk();

        $response->assertJsonPath('data.0.taxonomy', [[
            'type' => 'occasion',
            'key' => 'aniversario',
            'label' => 'Aniversário',
        ]]);
        self::assertStringNotContainsString('character', $response->getContent());
        self::assertStringNotContainsString('search_alias', $response->getContent());
    }

    public function test_large_page_uses_a_constant_query_budget_without_n_plus_one(): void
    {
        $category = $this->category('festas', 'Festas');
        for ($index = 1; $index <= 48; $index++) {
            $this->product($category, "produto-$index", "Produto $index", 'published', 'digital_ready');
        }

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson('/api/v1/catalog/products?per_page=48')->assertOk()->assertJsonCount(48, 'data');
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        self::assertLessThanOrEqual(6, $queryCount);
    }

    public function test_rate_limit_and_query_failure_are_sanitized(): void
    {
        config()->set('catalog.public_read_rate_limit_per_minute', 2);
        RateLimiter::clear(md5('public-catalogpublic-catalog:'.hash('sha256', '127.0.0.1')));
        $this->getJson('/api/v1/catalog/facets')->assertOk();
        $this->getJson('/api/v1/catalog/facets')->assertOk();
        $this->getJson('/api/v1/catalog/facets')->assertStatus(429)->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonStructure(['message', 'retry_after'])->assertJsonMissing(['trace']);

        RateLimiter::clear(md5('public-catalogpublic-catalog:'.hash('sha256', '127.0.0.1')));
        config()->set('catalog.public_read_rate_limit_per_minute', 240);
        $this->app->instance(PublicCatalogQuery::class, new class implements PublicCatalogQuery
        {
            public function list(PublicCatalogFilters $filters): PublicCatalogPage
            {
                throw new QueryException('pgsql', 'select storage_reference from catalog_products', [], new \PDOException('SQL secret'));
            }

            public function facets(): array
            {
                throw new QueryException('pgsql', 'select storage_reference from catalog_products', [], new \PDOException('SQL secret'));
            }

            public function findPublishedBySlug(string $slug): ?array
            {
                throw new QueryException('pgsql', 'select storage_reference from catalog_products', [], new \PDOException('SQL secret'));
            }
        });
        $this->getJson('/api/v1/catalog/products')->assertStatus(503)
            ->assertExactJson(['message' => 'Catálogo temporariamente indisponível.']);
    }

    public function test_internal_programming_errors_are_not_masked_as_catalog_unavailable(): void
    {
        $this->withoutExceptionHandling();
        $this->app->instance(PublicCatalogQuery::class, new class implements PublicCatalogQuery
        {
            public function list(PublicCatalogFilters $filters): PublicCatalogPage
            {
                throw new \RuntimeException('programming bug');
            }

            public function facets(): array
            {
                return [];
            }

            public function findPublishedBySlug(string $slug): ?array
            {
                return null;
            }
        });

        $this->expectException(\RuntimeException::class);
        $this->getJson('/api/v1/catalog/products');
    }

    private function category(string $slug, string $label): string
    {
        $id = (string) Str::uuid();
        DB::table('catalog_categories')->insert(compact('id', 'slug', 'label') + ['created_at' => now(), 'updated_at' => now()]);

        return $id;
    }

    private function product(string $categoryId, string $slug, string $name, string $status, string $modality): string
    {
        $id = (string) Str::uuid();
        DB::table('catalog_products')->insert([
            'id' => $id, 'slug' => $slug, 'name' => $name, 'description' => 'Descrição pública do produto',
            'modality' => $modality, 'status' => $status, 'category_id' => $categoryId,
            'price_minor' => 1290, 'currency' => 'EUR', 'availability' => 'available',
            'delivery_type' => $modality === 'physical_personalized' ? 'physical' : 'digital',
            'is_immediate_delivery' => $modality === 'digital_ready', 'compatibility' => 'Silhouette Studio',
            'published_at' => $status === 'published' ? now() : null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $id;
    }

    private function taxonomy(string $productId, string $type, string $label, string $key, bool $protected = false): void
    {
        $termId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert([
            'id' => $termId, 'type' => $type, 'label' => $label, 'canonical_key' => $key,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('catalog_product_taxonomy')->insert([
            'product_id' => $productId, 'taxonomy_term_id' => $termId, 'is_protected' => $protected,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function image(string $productId, string $reference): void
    {
        DB::table('catalog_product_images')->insert([
            'id' => (string) Str::uuid(), 'product_id' => $productId, 'storage_reference' => $reference,
            'alt_text' => 'Imagem do produto', 'sort_order' => 0, 'is_primary' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
