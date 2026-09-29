<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;
use App\Modules\Catalog\Application\Queries\PublicCatalogPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use App\Modules\Catalog\Application\Queries\PublicCatalogSitemapPage;
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

    public function test_detail_exposes_enriched_allowlist_gallery_and_models(): void
    {
        $this->app->instance(PublicCatalogImageResolver::class, new class implements PublicCatalogImageResolver
        {
            public function resolveBatch(array $references): array
            {
                return array_intersect_key([
                    'gallery-primary' => '/catalog-e2e-product.svg',
                    'gallery-secondary' => '/catalog-e2e-product.svg',
                    'unsafe-image' => 'https://unsafe.example/private.jpg',
                ], array_flip($references));
            }
        });
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'topo-fazendinha', 'Topo Fazendinha', 'published', 'physical_personalized');
        DB::table('catalog_products')->where('id', $product)->update([
            'materials' => 'Papel fotográfico 180g',
            'composition' => 'Topo de bolo em camadas',
            'file_description' => null,
            'usage_terms' => null,
            'minimum_quantity' => 12,
            'variants_reference' => 'admin-only',
        ]);
        $this->taxonomy($product, 'occasion', 'Aniversário', 'aniversario');
        $this->taxonomy($product, 'character', 'Personagem protegido', 'personagem-protegido', true);
        $this->image($product, 'gallery-primary', 20, true);
        $this->image($product, 'gallery-secondary', 30, false);
        $this->image($product, 'unsafe-image', 40, false);
        DB::table('catalog_product_models')->insert([
            [
                'id' => (string) Str::uuid(),
                'product_id' => $product,
                'public_key' => 'classico',
                'label' => 'Clássico',
                'difference' => 'Topo com camadas principais e acabamento simples.',
                'image_reference' => 'gallery-primary',
                'sort_order' => 10,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'product_id' => $product,
                'public_key' => 'premium',
                'label' => 'Premium',
                'difference' => 'Topo com mais camadas e acabamento reforçado.',
                'image_reference' => null,
                'sort_order' => 20,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/v1/catalog/products/topo-fazendinha')->assertOk();
        $data = $response->json('data');

        self::assertSame([
            'id', 'slug', 'name', 'description', 'category', 'modality', 'price_minor', 'currency', 'availability',
            'delivery_type', 'production_lead_time_days', 'is_immediate_delivery', 'primary_image', 'taxonomy',
            'compatibility', 'gallery', 'materials', 'composition', 'file_description', 'usage_terms',
            'minimum_quantity', 'models',
        ], array_keys($data));
        self::assertSame('/catalog-e2e-product.svg', $data['primary_image']['url']);
        self::assertSame([
            ['url' => '/catalog-e2e-product.svg', 'alt_text' => 'Imagem do produto'],
            ['url' => '/catalog-e2e-product.svg', 'alt_text' => 'Imagem do produto'],
        ], $data['gallery']);
        self::assertSame('Papel fotográfico 180g', $data['materials']);
        self::assertSame('Topo de bolo em camadas', $data['composition']);
        self::assertSame(12, $data['minimum_quantity']);
        self::assertSame([
            [
                'key' => 'classico',
                'label' => 'Clássico',
                'difference' => 'Topo com camadas principais e acabamento simples.',
                'is_default' => true,
                'image' => ['url' => '/catalog-e2e-product.svg', 'alt_text' => 'Clássico'],
            ],
            [
                'key' => 'premium',
                'label' => 'Premium',
                'difference' => 'Topo com mais camadas e acabamento reforçado.',
                'is_default' => false,
                'image' => null,
            ],
        ], $data['models']);
        self::assertStringNotContainsString('variants_reference', $response->getContent());
        self::assertStringNotContainsString('storage_reference', $response->getContent());
        self::assertStringNotContainsString('character', $response->getContent());
        self::assertStringNotContainsString('admin-only', $response->getContent());
    }

    public function test_detail_derives_required_default_model_when_product_has_no_model_rows(): void
    {
        $category = $this->category('digitais', 'Digitais');
        $this->product($category, 'arquivo-pronto', 'Arquivo pronto', 'published', 'digital_ready');

        $this->getJson('/api/v1/catalog/products/arquivo-pronto')->assertOk()
            ->assertJsonPath('data.models', [[
                'key' => 'padrao',
                'label' => 'Modelo padrão',
                'difference' => 'Versão padrão do produto.',
                'is_default' => true,
                'image' => null,
            ]]);
    }

    public function test_detail_falls_back_to_product_name_when_public_image_alt_is_empty(): void
    {
        $this->app->instance(PublicCatalogImageResolver::class, new class implements PublicCatalogImageResolver
        {
            public function resolveBatch(array $references): array
            {
                return ['safe-image' => '/catalog-e2e-product.svg'];
            }
        });
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'imagem-sem-alt', 'Imagem sem alt', 'published', 'digital_ready');
        DB::table('catalog_product_images')->insert([
            'id' => (string) Str::uuid(),
            'product_id' => $product,
            'storage_reference' => 'safe-image',
            'alt_text' => null,
            'sort_order' => 0,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson('/api/v1/catalog/products/imagem-sem-alt')->assertOk()
            ->assertJsonPath('data.primary_image.alt_text', 'Imagem sem alt')
            ->assertJsonPath('data.gallery.0.alt_text', 'Imagem sem alt');
    }

    public function test_detail_fails_closed_when_model_rows_are_invalid(): void
    {
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'modelos-invalidos', 'Modelos invalidos', 'published', 'digital_ready');
        DB::table('catalog_product_models')->insert([
            [
                'id' => (string) Str::uuid(),
                'product_id' => $product,
                'public_key' => 'primeiro',
                'label' => 'Primeiro',
                'difference' => 'Sem modelo padrao.',
                'image_reference' => null,
                'sort_order' => 10,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'product_id' => $product,
                'public_key' => 'segundo',
                'label' => 'Segundo',
                'difference' => 'Tambem sem modelo padrao.',
                'image_reference' => null,
                'sort_order' => 20,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->getJson('/api/v1/catalog/products/modelos-invalidos')->assertStatus(503)
            ->assertExactJson(['message' => 'Catálogo temporariamente indisponível.']);
    }

    public function test_model_image_reference_must_belong_to_the_same_product_public_images(): void
    {
        $this->app->instance(PublicCatalogImageResolver::class, new class implements PublicCatalogImageResolver
        {
            public function resolveBatch(array $references): array
            {
                return ['shared-image' => '/catalog-e2e-product.svg'];
            }
        });
        $category = $this->category('festas', 'Festas');
        $owner = $this->product($category, 'dono-da-imagem', 'Dono da imagem', 'published', 'digital_ready');
        $target = $this->product($category, 'modelo-cruzado', 'Modelo cruzado', 'published', 'digital_ready');
        $this->image($owner, 'shared-image');
        DB::table('catalog_product_models')->insert([
            'id' => (string) Str::uuid(),
            'product_id' => $target,
            'public_key' => 'premium',
            'label' => 'Premium',
            'difference' => 'Referencia imagem de outro produto.',
            'image_reference' => 'shared-image',
            'sort_order' => 10,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson('/api/v1/catalog/products/modelo-cruzado')->assertOk()
            ->assertJsonPath('data.models.0.image', null);
    }

    public function test_detail_resource_strips_nested_private_fields_from_alternative_adapter(): void
    {
        $category = $this->category('festas', 'Festas');
        $this->product($category, 'projecao-publica', 'Projecao publica', 'published', 'digital_ready');
        $projection = $this->app->make(PublicCatalogQuery::class)->findPublishedBySlug('projecao-publica');
        $image = ['url' => '/catalog-e2e-product.svg', 'alt_text' => 'Produto', 'storage_reference' => 'private/evidence'];
        $projection['gallery'] = [$image, ['url' => 'https://unsafe.example/private.jpg', 'alt_text' => 'Privado']];
        $projection['models'][0]['image'] = $image;
        $projection['models'][0]['admin_notes'] = 'private/evidence';
        $query = \Mockery::mock(PublicCatalogQuery::class);
        $query->shouldReceive('findPublishedBySlug')->with('projecao-publica')->once()->andReturn($projection);
        $this->app->instance(PublicCatalogQuery::class, $query);

        $response = $this->getJson('/api/v1/catalog/products/projecao-publica')->assertOk();
        $response->assertJsonPath('data.gallery', [['url' => '/catalog-e2e-product.svg', 'alt_text' => 'Produto']]);
        self::assertSame(['key', 'label', 'difference', 'is_default', 'image'], array_keys($response->json('data.models.0')));
        self::assertSame(['url', 'alt_text'], array_keys($response->json('data.models.0.image')));
        self::assertStringNotContainsString('private/evidence', $response->getContent());
        self::assertStringNotContainsString('storage_reference', $response->getContent());
        self::assertStringNotContainsString('admin_notes', $response->getContent());
    }

    public function test_detail_bounds_gallery_and_models_with_a_constant_query_budget(): void
    {
        $this->app->instance(PublicCatalogImageResolver::class, new class implements PublicCatalogImageResolver
        {
            public function resolveBatch(array $references): array
            {
                return array_fill_keys($references, '/catalog-e2e-product.svg');
            }
        });
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'detalhe-limites', 'Detalhe limites', 'published', 'digital_ready');
        for ($index = 0; $index < 9; $index++) {
            $this->image($product, "image-$index", $index, $index === 0);
        }
        for ($index = 0; $index < 12; $index++) {
            DB::table('catalog_product_models')->insert([
                'id' => (string) Str::uuid(), 'product_id' => $product, 'public_key' => "modelo-$index",
                'label' => "Modelo $index", 'difference' => 'Diferenca publica.',
                'sort_order' => $index, 'is_default' => $index === 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();
        try {
            $this->getJson('/api/v1/catalog/products/detalhe-limites')->assertOk()
                ->assertJsonCount(8, 'data.gallery')->assertJsonCount(12, 'data.models');
            self::assertLessThanOrEqual(8, count(DB::getQueryLog()));
        } finally {
            DB::disableQueryLog();
        }

        DB::table('catalog_product_models')->insert([
            'id' => (string) Str::uuid(), 'product_id' => $product, 'public_key' => 'excedente',
            'label' => 'Excedente', 'difference' => 'Modelo fora do limite.',
            'sort_order' => 12, 'is_default' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->getJson('/api/v1/catalog/products/detalhe-limites')->assertStatus(503)
            ->assertExactJson(['message' => 'Catálogo temporariamente indisponível.']);
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

    public function test_listing_resolves_only_primary_images(): void
    {
        $resolver = new class implements PublicCatalogImageResolver
        {
            /** @var list<string> */
            public array $references = [];

            public function resolveBatch(array $references): array
            {
                $this->references = array_values($references);

                return array_fill_keys($references, '/catalog-e2e-product.svg');
            }
        };
        $this->app->instance(PublicCatalogImageResolver::class, $resolver);
        $category = $this->category('festas', 'Festas');
        $product = $this->product($category, 'produto-com-galeria', 'Produto com galeria', 'published', 'digital_ready');
        $this->image($product, 'primary-image', 0, true);
        $this->image($product, 'secondary-image', 10, false);

        $this->getJson('/api/v1/catalog/products')->assertOk()
            ->assertJsonPath('data.0.primary_image.url', '/catalog-e2e-product.svg');

        self::assertSame(['primary-image'], $resolver->references);
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

            public function sitemapPage(int $page): PublicCatalogSitemapPage
            {
                throw new \LogicException('Not used');
            }

            public function sitemapCategories(): array
            {
                throw new \LogicException('Not used');
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

            public function sitemapPage(int $page): PublicCatalogSitemapPage
            {
                throw new \LogicException('Not used');
            }

            public function sitemapCategories(): array
            {
                throw new \LogicException('Not used');
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

            public function sitemapPage(int $page): PublicCatalogSitemapPage
            {
                throw new \LogicException('Not used');
            }

            public function sitemapCategories(): array
            {
                throw new \LogicException('Not used');
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

    private function image(string $productId, string $reference, int $sortOrder = 0, bool $primary = true): void
    {
        DB::table('catalog_product_images')->insert([
            'id' => (string) Str::uuid(), 'product_id' => $productId, 'storage_reference' => $reference,
            'alt_text' => 'Imagem do produto', 'sort_order' => $sortOrder, 'is_primary' => $primary,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
