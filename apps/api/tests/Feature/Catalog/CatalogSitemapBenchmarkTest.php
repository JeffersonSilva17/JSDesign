<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CatalogSitemapBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_and_last_batches_at_one_hundred_thousand_products(): void
    {
        if (getenv('CATALOG_SITEMAP_BENCHMARK') !== '1') {
            $this->markTestSkipped('Benchmark explícito: CATALOG_SITEMAP_BENCHMARK=1.');
        }
        DB::statement("INSERT INTO catalog_categories (id, slug, label, created_at, updated_at) VALUES ('00000000-0000-4000-8000-000000000001', 'benchmark', 'Benchmark', now(), now())");
        DB::statement(<<<'SQL'
            INSERT INTO catalog_products (id, slug, name, description, category_id, status, modality, price_minor, currency, availability, delivery_type, is_immediate_delivery, published_at, created_at, updated_at)
            SELECT md5(i::text)::uuid, 'benchmark-' || i, 'Produto', 'Descrição pública', '00000000-0000-4000-8000-000000000001'::uuid,
                   'published', 'digital_ready', 100, 'EUR', 'available', 'digital', true, now(), now(), now()
            FROM generate_series(1, 100000) i
        SQL);
        DB::statement('ANALYZE catalog_products');
        DB::statement('ANALYZE catalog_categories');
        $query = app(PublicCatalogQuery::class);
        $evidence = [];
        foreach ([1, 200] as $number) {
            DB::enableQueryLog();
            DB::flushQueryLog();
            $start = hrtime(true);
            $page = $query->sitemapPage($number);
            $elapsed = (hrtime(true) - $start) / 1000000;
            $statements = array_values(array_filter(DB::getQueryLog(), fn (array $entry): bool => str_contains($entry['query'], 'catalog_products')));
            DB::disableQueryLog();
            self::assertCount(500, $page->slugs);
            self::assertSame(100000, $page->total);
            self::assertLessThanOrEqual(2, count($statements));
            self::assertLessThan(3000, $elapsed);
            $plan = DB::select('EXPLAIN (ANALYZE, BUFFERS) '.$statements[0]['query'], $statements[0]['bindings']);
            $evidence[] = ['page' => $number, 'milliseconds' => $elapsed, 'data_queries' => count($statements), 'plan' => $plan];
        }
        fwrite(STDOUT, "\nSITEMAP_BENCHMARK ".json_encode($evidence, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");
    }
}
