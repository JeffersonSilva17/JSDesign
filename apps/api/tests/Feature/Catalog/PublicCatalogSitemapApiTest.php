<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

final class PublicCatalogSitemapApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear(md5('public-catalog-sitemappublic-catalog-sitemap:'.hash('sha256', '127.0.0.1')));
    }

    public function test_all_published_slugs_are_reachable_and_withdrawal_is_immediate(): void
    {
        $this->seedProducts(501);
        $first = $this->getJson('/api/v1/catalog/sitemap?page=1')->assertOk()->assertJsonCount(500, 'data')->assertJsonPath('meta.total', 501);
        $last = $this->getJson('/api/v1/catalog/sitemap?page=2')->assertOk()->assertJsonCount(1, 'data');
        $slugs = array_merge(array_column($first->json('data'), 'slug'), array_column($last->json('data'), 'slug'));
        self::assertCount(501, array_unique($slugs));
        self::assertSame(['slug'], array_keys($first->json('data.0')));
        DB::table('catalog_products')->where('slug', $slugs[0])->update(['status' => 'draft', 'published_at' => null]);
        $this->getJson('/api/v1/catalog/sitemap?page=1')->assertOk()->assertJsonPath('meta.total', 500)->assertJsonMissing(['slug' => $slugs[0]]);
        $this->getJson('/api/v1/catalog/sitemap?page=2')->assertNotFound();
        $this->getJson('/api/v1/catalog/sitemap-facets')->assertOk()->assertExactJson(['data' => ['categories' => [['slug' => 'festas', 'label' => 'Festas']]]]);
    }

    public function test_empty_bounds_strict_query_and_crawl_isolation(): void
    {
        $this->getJson('/api/v1/catalog/sitemap?page=1')->assertOk()->assertExactJson(['data' => [], 'meta' => ['current_page' => 1, 'per_page' => 500, 'last_page' => 1, 'total' => 0]])->assertHeader('Cache-Control', 'no-store, private');
        foreach (['', '?page=0', '?page=01', '?page=10001', '?page=1&page=2', '?page[]=1', '?page=1&per_page=500'] as $query) {
            $this->getJson('/api/v1/catalog/sitemap'.$query)->assertUnprocessable()->assertHeader('Cache-Control', 'no-store, private');
        }
        $this->getJson('/api/v1/catalog/sitemap-facets?x=1')->assertUnprocessable()->assertHeader('Cache-Control', 'no-store, private');
        config(['catalog.sitemap_rate_limit_per_minute' => 1]);
        $this->getJson('/api/v1/catalog/sitemap?page=1')->assertStatus(429);
        $this->getJson('/api/v1/catalog/products')->assertOk();
        $this->getJson('/api/v1/catalog/facets')->assertOk();
        $this->getJson('/api/v1/catalog/search?q=convite')->assertOk();
    }

    public function test_signed_clients_have_isolated_budgets_and_forged_headers_cannot_bypass_them(): void
    {
        $key = bin2hex(random_bytes(32));
        config(['catalog.sitemap_client_key' => $key, 'catalog.sitemap_rate_limit_per_minute' => 1]);
        $token = static function (string $ip, int $time) use ($key): string {
            $id = hash_hmac('sha256', 'sitemap-ip:v1:'.$ip, $key);

            return $id.'.'.$time.'.'.hash_hmac('sha256', 'sitemap-client:v1:'.$id.':'.$time, $key);
        };
        $first = $token('192.0.2.1', time());
        $second = $token('192.0.2.2', time());
        $this->withHeader('X-Catalog-Client', $first)->getJson('/api/v1/catalog/sitemap?page=1')->assertOk();
        $this->withHeader('X-Catalog-Client', $first)->withHeader('X-Forwarded-For', '192.0.2.99')->getJson('/api/v1/catalog/sitemap?page=1')->assertStatus(429);
        $this->withHeader('X-Catalog-Client', $second)->getJson('/api/v1/catalog/sitemap?page=1')->assertOk();
        $this->withHeader('X-Catalog-Client', $token('192.0.2.3', time() - 60))->getJson('/api/v1/catalog/sitemap?page=1')->assertForbidden();
        $this->withHeader('X-Catalog-Client', $first.'0')->getJson('/api/v1/catalog/sitemap?page=1')->assertForbidden();
        $this->flushHeaders();
        $this->withHeader('X-Forwarded-For', '192.0.2.90')->getJson('/api/v1/catalog/sitemap?page=1')->assertOk();
        $this->withHeader('X-Forwarded-For', '192.0.2.91')->getJson('/api/v1/catalog/sitemap?page=1')->assertStatus(429);
    }

    private function seedProducts(int $count): void
    {
        $category = (string) Str::uuid();
        DB::table('catalog_categories')->insert(['id' => $category, 'slug' => 'festas', 'label' => 'Festas', 'created_at' => now(), 'updated_at' => now()]);
        $rows = [];
        for ($index = 0; $index <= $count; $index++) {
            $rows[] = ['id' => (string) Str::uuid(), 'slug' => 'produto-'.$index, 'name' => 'Produto', 'description' => 'Público', 'category_id' => $category, 'status' => $index < $count ? 'published' : 'draft', 'modality' => 'digital_ready', 'price_minor' => 100, 'currency' => 'EUR', 'availability' => 'available', 'delivery_type' => 'digital', 'is_immediate_delivery' => true, 'published_at' => $index < $count ? now() : null, 'created_at' => now(), 'updated_at' => now()];
        }
        DB::table('catalog_products')->insert($rows);
    }

    public function test_editorial_limit_is_not_silently_truncated(): void
    {
        DB::statement("INSERT INTO catalog_categories (id, slug, label, created_at, updated_at) SELECT md5(i::text)::uuid, 'categoria-' || i, 'Categoria ' || i, now(), now() FROM generate_series(1, 4998) i");
        DB::statement(<<<'SQL'
            INSERT INTO catalog_products (id, slug, name, description, category_id, status, modality, price_minor, currency, availability, delivery_type, is_immediate_delivery, published_at, created_at, updated_at)
            SELECT id, slug, 'Produto', 'Público', id, 'published', 'digital_ready', 100, 'EUR', 'available', 'digital', true, now(), now(), now() FROM catalog_categories
        SQL);
        $this->getJson('/api/v1/catalog/sitemap-facets')->assertOk()->assertJsonCount(4998, 'data.categories');
        $this->seedProducts(1);
        $this->getJson('/api/v1/catalog/sitemap-facets')->assertStatus(503)->assertExactJson(['message' => 'Catálogo temporariamente indisponível.']);
    }

    public function test_invalid_public_slugs_fail_closed_in_sitemap_outputs(): void
    {
        $category = (string) Str::uuid();
        DB::table('catalog_categories')->insert(['id' => $category, 'slug' => 'festas', 'label' => 'Festas', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('catalog_products')->insert(['id' => (string) Str::uuid(), 'slug' => 'produto_invalido', 'name' => 'Produto', 'description' => 'Público', 'category_id' => $category, 'status' => 'published', 'modality' => 'digital_ready', 'price_minor' => 100, 'currency' => 'EUR', 'availability' => 'available', 'delivery_type' => 'digital', 'is_immediate_delivery' => true, 'published_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $this->getJson('/api/v1/catalog/sitemap?page=1')
            ->assertStatus(503)
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertExactJson(['message' => 'Catálogo temporariamente indisponível.'])
            ->assertJsonMissing(['slug' => 'produto_invalido']);

        DB::table('catalog_categories')->where('id', $category)->update(['slug' => 'festas_invalidas']);
        DB::table('catalog_products')->update(['slug' => 'produto-valido']);
        $this->getJson('/api/v1/catalog/sitemap-facets')
            ->assertStatus(503)
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertExactJson(['message' => 'Catálogo temporariamente indisponível.'])
            ->assertJsonMissing(['slug' => 'festas_invalidas']);
    }

    public function test_query_budget_and_dependency_failures_are_sanitized(): void
    {
        $this->seedProducts(501);
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->getJson('/api/v1/catalog/sitemap?page=2')->assertOk();
        $queries = array_filter(DB::getQueryLog(), fn (array $query): bool => str_contains($query['query'], 'catalog_'));
        DB::disableQueryLog();
        self::assertLessThanOrEqual(2, count($queries));
        foreach ($queries as $query) {
            self::assertStringNotContainsString('catalog_product_images', $query['query']);
            self::assertStringNotContainsString('description', $query['query']);
        }
    }

    public function test_dependency_failures_are_sanitized(): void
    {
        $port = Mockery::mock(PublicCatalogQuery::class);
        $port->shouldReceive('sitemapPage')->andThrow(new \PDOException('private SQL'));
        $port->shouldReceive('sitemapCategories')->andThrow(new \OverflowException('private categories'));
        $this->app->instance(PublicCatalogQuery::class, $port);
        foreach (['/api/v1/catalog/sitemap?page=1', '/api/v1/catalog/sitemap-facets'] as $path) {
            $this->getJson($path)->assertStatus(503)->assertHeader('Retry-After', '60')->assertHeader('Cache-Control', 'no-store, private')->assertExactJson(['message' => 'Catálogo temporariamente indisponível.']);
        }
    }
}
