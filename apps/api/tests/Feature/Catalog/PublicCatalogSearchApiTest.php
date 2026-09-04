<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogSearchCriteria;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchQuery;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

final class PublicCatalogSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear(md5('public-catalog-searchpublic-catalog-search:'.hash('sha256', '127.0.0.1')));
    }

    public function test_rejects_empty_raw_query_segments(): void
    {
        $this->getJson('/api/v1/catalog/search?q=convite&&page=1')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/search?&q=convite')->assertUnprocessable();
        $this->getJson('/api/v1/catalog/search?q=convite&')->assertUnprocessable();
    }

    public function test_searches_only_allowed_sources_on_published_products_and_verified_characters(): void
    {
        $category = $this->category('convites', 'Convites');
        $byName = $this->product($category, 'bosque', 'Bosque Encantado');
        $byTheme = $this->product($category, 'festa-real', 'Festa Real');
        $byAlias = $this->product($category, 'celebracao', 'Celebração');
        $byCharacter = $this->product($category, 'heroi', 'Herói');
        $unverified = $this->product($category, 'nao-verificado', 'Outro modelo');
        $draft = $this->product($category, 'rascunho', 'Bosque privado', 'draft');
        $descriptionOnly = $this->product($category, 'descricao', 'Sem relação', 'published', 'segredo administrativo bosque');
        DB::table('catalog_products')->where('id', $descriptionOnly)->update(['compatibility' => 'Bosque']);

        $this->taxonomy($byTheme, 'theme', 'Bosque', 'bosque');
        $this->taxonomy($byAlias, 'search_alias', 'Bosque', 'bosque-alias');
        $this->taxonomy($byCharacter, 'character', 'Bosque', 'bosque-personagem', true, 'verified');
        $this->taxonomy($unverified, 'character', 'Bosque', 'bosque-pendente', true, 'pending');
        $this->taxonomy($draft, 'theme', 'Bosque', 'bosque-privado');

        $response = $this->getJson('/api/v1/catalog/search?q=bosque&per_page=48')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');

        $ids = $this->resultIds($response->json());
        self::assertEqualsCanonicalizing([$byName, $byTheme, $byAlias, $byCharacter], $ids);
        self::assertNotContains($unverified, $ids);
        self::assertNotContains($draft, $ids);
        self::assertNotContains($descriptionOnly, $ids);
        self::assertStringNotContainsString('character', $response->getContent());
        self::assertStringNotContainsString('search_alias', $response->getContent());
        self::assertStringNotContainsString('verification_', $response->getContent());
        self::assertStringNotContainsString('storage_reference', $response->getContent());
    }

    public function test_searches_category_modality_and_occasion_as_public_sources(): void
    {
        $category = $this->category('lembrancas', 'Lembranças');
        $product = $this->product($category, 'modelo-neutro', 'Modelo neutro');
        $this->taxonomy($product, 'occasion', 'Casamento', 'casamento');

        foreach (['lembrancas', 'produto digital', 'casamento'] as $query) {
            self::assertContains($product, $this->resultIds(
                $this->getJson('/api/v1/catalog/search?q='.rawurlencode($query))->assertOk()->json(),
            ));
        }

        $this->getJson('/api/v1/catalog/search?q=%25_')->assertOk()->assertJsonPath('meta.total', 0);
        $this->getJson('/api/v1/catalog/search?q='.rawurlencode("' OR 1=1 --"))->assertOk()->assertJsonPath('meta.total', 0);
    }

    public function test_normalizes_ranks_groups_and_paginates_exact_before_similar_deterministically(): void
    {
        $convites = $this->category('convites', 'Convites');
        $festas = $this->category('festas', 'Festas');
        $exactName = $this->product($convites, 'convite', 'Convite', publishedAt: '2026-08-20 12:00:00+00');
        $exactTheme = $this->product($festas, 'tema-exato', 'Modelo festa', publishedAt: '2026-08-21 12:00:00+00');
        $prefix = $this->product($convites, 'convite-floral', 'Convite Floral', publishedAt: '2026-08-22 12:00:00+00');
        $fuzzy = $this->product($festas, 'convte-minimalista', 'Convte', publishedAt: '2026-08-23 12:00:00+00');
        $this->taxonomy($exactTheme, 'theme', 'Convite', 'convite');

        $first = $this->getJson('/api/v1/catalog/search?q=%20CONV%C3%8DTE%20&page=1&per_page=2')->assertOk();
        self::assertSame([$exactName, $exactTheme], $this->resultIds($first->json()));
        $first->assertJsonPath('meta.query', 'CONVÍTE')->assertJsonPath('meta.total_exact', 2)
            ->assertJsonPath('meta.total_similar', 2)->assertJsonPath('meta.last_page', 2);
        self::assertSame(['convites', 'festas'], array_map(
            static fn (array $group): string => $group['category']['slug'],
            $first->json('data.exact_groups'),
        ));

        $second = $this->getJson('/api/v1/catalog/search?q=convite&page=2&per_page=2')->assertOk();
        self::assertSame([$prefix, $fuzzy], $this->resultIds($second->json()));
        $second->assertJsonCount(0, 'data.exact_groups')->assertJsonCount(2, 'data.similar');

        $this->getJson('/api/v1/catalog/search?q=convite&page=99&per_page=2')->assertOk()
            ->assertJsonPath('meta.current_page', 99)->assertJsonPath('meta.last_page', 2)
            ->assertJsonCount(0, 'data.exact_groups')->assertJsonCount(0, 'data.similar');
    }

    public function test_empty_result_preserves_out_of_range_page_without_invitation_when_not_applicable(): void
    {
        $this->getJson('/api/v1/catalog/search?q=semresultado&page=99&per_page=12')->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('meta.current_page', 99)
            ->assertJsonPath('meta.last_page', 1)
            ->assertJsonPath('data.intent.type', 'generic')
            ->assertJsonCount(0, 'data.exact_groups')
            ->assertJsonCount(0, 'data.similar');
    }

    public function test_invitation_intent_is_not_shown_when_search_has_published_results(): void
    {
        $category = $this->category('convites', 'Convites');
        $this->product($category, 'convite-existente', 'Convite de Sereia');

        $this->getJson('/api/v1/catalog/search?q=convite%20de%20sereia')->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.intent.type', 'generic')
            ->assertJsonPath('data.intent.handoff_href', null);
    }

    public function test_sql_normalizer_collapses_catalog_value_spacing(): void
    {
        $category = $this->category('convites', 'Convites');
        $product = $this->product($category, 'convite-espaco', "Convite\u{00A0}  Ágil");
        $fullWidth = $this->product($category, 'convite-fullwidth', 'Ｃｏｎｖｉｔｅ Ágil');

        $response = $this->getJson('/api/v1/catalog/search?q=convite%20%C3%A1gil')->assertOk();

        self::assertEqualsCanonicalizing([$product, $fullWidth], $this->resultIds($response->json()));
        $response->assertJsonPath('meta.total_exact', 2);
    }

    public function test_candidate_limit_does_not_truncate_non_fuzzy_totals_or_later_pages(): void
    {
        config()->set('catalog.search_candidate_limit', 50);
        $category = $this->category('convites', 'Convites');
        for ($index = 1; $index <= 55; $index++) {
            $this->product($category, sprintf('convite-lote-%02d', $index), sprintf('Convite Lote %02d', $index));
        }

        $first = $this->getJson('/api/v1/catalog/search?q=convite&per_page=48')->assertOk();
        $first->assertJsonPath('meta.total', 55)
            ->assertJsonPath('meta.total_similar', 55)
            ->assertJsonPath('meta.last_page', 2);

        $second = $this->getJson('/api/v1/catalog/search?q=convite&page=2&per_page=48')->assertOk();
        $second->assertJsonCount(7, 'data.similar');
    }

    public function test_exact_groups_use_label_and_slug_as_secondary_tie_breakers(): void
    {
        $zebra = $this->category('zebra', 'Zebra');
        $alfa = $this->category('alfa', 'Alfa');
        $beta = $this->category('beta', 'Alfa');
        $this->product($zebra, 'tema-zebra', 'Outro produto');
        $alfaProduct = $this->product($alfa, 'tema-alfa', 'Outro produto');
        $betaProduct = $this->product($beta, 'tema-beta', 'Outro produto');
        $this->taxonomy($alfaProduct, 'theme', 'Convite', 'convite-alfa');
        $this->taxonomy($betaProduct, 'theme', 'Convite', 'convite-beta');

        $response = $this->getJson('/api/v1/catalog/search?q=convite&per_page=12')->assertOk();

        self::assertSame(['alfa', 'beta'], array_map(
            static fn (array $group): string => $group['category']['slug'],
            $response->json('data.exact_groups'),
        ));
    }

    public function test_contract_is_closed_coherent_and_suggestions_are_safe_editorial_values(): void
    {
        $category = $this->category('convites', 'Convites Digitais');
        $product = $this->product($category, 'convite-natal', 'Convite Natal');
        $this->taxonomy($product, 'theme', 'Natal', 'natal');
        $this->taxonomy($product, 'occasion', 'Aniversário', 'aniversario');
        $this->taxonomy($product, 'search_alias', 'Xmas', 'xmas');

        $payload = $this->getJson('/api/v1/catalog/search?q=natal')->assertOk()->json();

        self::assertSame(['data', 'meta'], array_keys($payload));
        self::assertSame(['exact_groups', 'similar', 'suggestions', 'intent'], array_keys($payload['data']));
        self::assertSame(['query', 'current_page', 'per_page', 'last_page', 'total', 'total_exact', 'total_similar'], array_keys($payload['meta']));
        self::assertSame($payload['meta']['total'], $payload['meta']['total_exact'] + $payload['meta']['total_similar']);
        self::assertLessThanOrEqual(6, count($payload['data']['suggestions']));
        self::assertSame(count($payload['data']['suggestions']), count(array_unique(array_column($payload['data']['suggestions'], 'href'))));
        foreach ($payload['data']['suggestions'] as $suggestion) {
            self::assertMatchesRegularExpression('#^/(?!/)#', $suggestion['href']);
            self::assertNotSame('Xmas', $suggestion['label']);
        }
        self::assertSame(['type', 'preserved_term', 'handoff_href'], array_keys($payload['data']['intent']));
    }

    public function test_suggestions_skip_labels_that_cannot_be_submitted_as_search_queries(): void
    {
        $longCategoryLabel = str_repeat('Convite', 21);
        $longCategory = $this->category('convites', $longCategoryLabel);
        $product = $this->product($longCategory, 'convite-longo', 'Convite Longo');
        $this->taxonomy($product, 'theme', "Convite\nSecreto", 'convite-secreto');

        $payload = $this->getJson('/api/v1/catalog/search?q=convite')->assertOk()->json();
        $labels = array_column($payload['data']['suggestions'], 'label');

        self::assertNotContains($longCategoryLabel, $labels);
        self::assertNotContains("Convite\nSecreto", $labels);
        foreach ($payload['data']['suggestions'] as $suggestion) {
            $this->getJson($suggestion['href'])->assertOk();
        }
    }

    public function test_invitation_intent_is_backend_owned_and_preserves_a_safe_handoff(): void
    {
        $response = $this->getJson('/api/v1/catalog/search?q=convite%20de%20sereia')->assertOk();

        $response->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data.intent.type', 'invitation')
            ->assertJsonPath('data.intent.preserved_term', 'convite de sereia');
        self::assertMatchesRegularExpression('#^/(?!/)#', (string) $response->json('data.intent.handoff_href'));
    }

    public function test_suggestions_are_ordered_by_relevance_before_label(): void
    {
        $this->product($this->category('aaa-distante', 'Aaa Distante'), 'aaa-distante', 'Aaa Distante');
        $this->product($this->category('convites-digitais', 'Convites Digitais'), 'convite-digital', 'Convite Digital');
        $this->product($this->category('zz-convite', 'ZZ Convite'), 'zz-convite', 'ZZ Convite');

        $response = $this->getJson('/api/v1/catalog/search?q=convite')->assertOk();

        self::assertSame('Convites Digitais', $response->json('data.suggestions.0.label'));
    }

    public function test_rejects_malformed_queries_and_sanitizes_throttle_and_database_failure(): void
    {
        foreach ([
            '/api/v1/catalog/search',
            '/api/v1/catalog/search?q=a',
            '/api/v1/catalog/search?q=%20%20',
            '/api/v1/catalog/search?q=convite&q=outro',
            '/api/v1/catalog/search?q[]=convite',
            '/api/v1/catalog/search?q=convite&page=01',
            '/api/v1/catalog/search?q=convite&page=1001',
            '/api/v1/catalog/search?q=convite&per_page=49',
            '/api/v1/catalog/search?q=convite%00secreto',
            '/api/v1/catalog/search?q=%E0%A4%A',
        ] as $uri) {
            $this->getJson($uri)->assertUnprocessable()->assertHeader('Cache-Control', 'no-store, private');
        }

        config()->set('catalog.search_rate_limit_per_minute', 1);
        RateLimiter::clear(md5('public-catalog-searchpublic-catalog-search:'.hash('sha256', '127.0.0.1')));
        $this->getJson('/api/v1/catalog/search?q=convite')->assertOk();
        $this->getJson('/api/v1/catalog/search?q=convite')->assertStatus(429)
            ->assertHeader('Cache-Control', 'no-store, private')->assertJsonMissing(['trace']);

        RateLimiter::clear(md5('public-catalog-searchpublic-catalog-search:'.hash('sha256', '127.0.0.1')));
        config()->set('catalog.search_rate_limit_per_minute', 60);
        $this->app->instance(PublicCatalogSearchQuery::class, new class implements PublicCatalogSearchQuery
        {
            public function search(PublicCatalogSearchCriteria $criteria): PublicCatalogSearchPage
            {
                throw new QueryException('pgsql', 'select secret from catalog_products', [], new \PDOException('SQL secret'));
            }
        });
        $this->app['router']->getRoutes()->getByName('api.v1.catalog.search')?->flushController();

        $this->getJson('/api/v1/catalog/search?q=convite')->assertStatus(503)
            ->assertExactJson(['message' => 'Busca temporariamente indisponível.']);
    }

    public function test_search_uses_a_bounded_query_budget_and_unpublication_is_immediate(): void
    {
        $category = $this->category('convites', 'Convites');
        $ids = [];
        for ($index = 1; $index <= 20; $index++) {
            $ids[] = $this->product($category, "convite-$index", "Convite $index");
        }

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson('/api/v1/catalog/search?q=convite&per_page=20')->assertOk();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();
        self::assertLessThanOrEqual(10, $queryCount);

        DB::table('catalog_products')->where('id', $ids[0])->update(['status' => 'unpublished', 'unpublished_at' => now()]);
        self::assertNotContains($ids[0], $this->resultIds($this->getJson('/api/v1/catalog/search?q=convite&per_page=20')->assertOk()->json()));
    }

    public function test_public_read_and_search_statement_timeout_clamps_are_separate(): void
    {
        config()->set('catalog.public_read_statement_timeout_ms', 30000);
        config()->set('catalog.search_statement_timeout_ms', 30000);
        $timeouts = [];
        DB::listen(static function (QueryExecuted $query) use (&$timeouts): void {
            if (str_contains($query->sql, "set_config('statement_timeout'")) {
                $timeouts[] = $query->bindings[0] ?? null;
            }
        });

        $this->getJson('/api/v1/catalog/facets')->assertOk();
        $this->getJson('/api/v1/catalog/search?q=convite')->assertOk();

        self::assertContains('30000ms', $timeouts);
        self::assertContains('5000ms', $timeouts);
    }

    public function test_suggestion_failure_does_not_discard_main_results(): void
    {
        $category = $this->category('convites', 'Convites');
        $product = $this->product($category, 'convite-principal', 'Convite');
        DB::listen(static function (QueryExecuted $query): void {
            if (str_contains($query->sql, 'WITH editorial AS')) {
                throw new \PDOException('forced complementary suggestion failure');
            }
        });

        $payload = $this->getJson('/api/v1/catalog/search?q=convite')->assertOk()->json();

        self::assertContains($product, $this->resultIds($payload));
        self::assertSame([], $payload['data']['suggestions']);
    }

    public function test_search_extensions_and_expression_index_are_available_to_the_real_predicate(): void
    {
        $extensions = DB::table('pg_extension')->whereIn('extname', ['pg_trgm', 'unaccent'])->pluck('extname')->all();
        self::assertEqualsCanonicalizing(['pg_trgm', 'unaccent'], $extensions);
        self::assertSame('i', DB::table('pg_proc')->where('proname', 'catalog_public_search_normalize_v1')->value('provolatile'));
        self::assertStringContainsString(
            'REINDEX',
            (string) DB::table('pg_proc as p')
                ->leftJoin('pg_description as d', 'd.objoid', '=', 'p.oid')
                ->where('p.proname', 'catalog_public_search_normalize_v1')
                ->value('d.description'),
        );

        $category = $this->category('convites', 'Convites');
        DB::insert(<<<'SQL'
            INSERT INTO catalog_products (
                id, slug, name, description, modality, status, category_id, price_minor, currency,
                availability, delivery_type, is_immediate_delivery, published_at, created_at, updated_at
            )
            SELECT md5('search-index-' || value::text)::uuid, 'modelo-' || value::text,
                   CASE WHEN value = 2500 THEN 'Convte' ELSE 'Modelo ' || value::text END,
                   'Descrição pública', 'digital_ready', 'published', ?::uuid, 1290, 'EUR',
                   'available', 'digital', true, now(), now(), now()
            FROM generate_series(1, 5000) AS value
        SQL, [$category]);
        DB::statement('ANALYZE catalog_products');
        $endpointQuery = null;
        DB::listen(static function (QueryExecuted $query) use (&$endpointQuery): void {
            if (str_contains($query->sql, 'WITH params AS') && str_contains($query->sql, 'match_values AS')) {
                $endpointQuery = [$query->sql, $query->bindings];
            }
        });
        $this->getJson('/api/v1/catalog/search?q=convite')->assertOk();
        self::assertNotNull($endpointQuery, 'A consulta SQL real do endpoint não foi capturada.');
        self::assertStringContainsString('non_fuzzy_best', $endpointQuery[0]);
        self::assertStringNotContainsString('FROM non_fuzzy_best CROSS JOIN params', $endpointQuery[0]);
        self::assertStringContainsString('JOIN catalog_products p ON p.id = fuzzy_best.product_id', $endpointQuery[0]);
        $plan = implode("\n", array_map(
            static fn (object $row): string => (string) $row->{'QUERY PLAN'},
            DB::select('EXPLAIN '.$endpointQuery[0], $endpointQuery[1]),
        ));

        self::assertStringContainsString('catalog_products_search_name_trgm_idx', $plan);
    }

    /** @param array<string, mixed> $payload @return list<string> */
    private function resultIds(array $payload): array
    {
        $exact = [];
        foreach ($payload['data']['exact_groups'] as $group) {
            array_push($exact, ...array_column($group['items'], 'id'));
        }

        return [...$exact, ...array_column($payload['data']['similar'], 'id')];
    }

    private function category(string $slug, string $label): string
    {
        $id = (string) Str::uuid();
        DB::table('catalog_categories')->insert(compact('id', 'slug', 'label') + ['created_at' => now(), 'updated_at' => now()]);

        return $id;
    }

    private function product(
        string $categoryId,
        string $slug,
        string $name,
        string $status = 'published',
        string $description = 'Descrição pública',
        string $publishedAt = '2026-08-20 12:00:00+00',
    ): string {
        $id = (string) Str::uuid();
        DB::table('catalog_products')->insert([
            'id' => $id, 'slug' => $slug, 'name' => $name, 'description' => $description,
            'modality' => 'digital_ready', 'status' => $status, 'category_id' => $categoryId,
            'price_minor' => 1290, 'currency' => 'EUR', 'availability' => 'available', 'delivery_type' => 'digital',
            'is_immediate_delivery' => true, 'compatibility' => null,
            'published_at' => $status === 'published' ? $publishedAt : null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $id;
    }

    private function taxonomy(
        string $productId,
        string $type,
        string $label,
        string $key,
        bool $protected = false,
        ?string $verification = null,
    ): void {
        $termId = (string) Str::uuid();
        DB::table('catalog_taxonomy_terms')->insert([
            'id' => $termId, 'type' => $type, 'label' => $label, 'canonical_key' => $key,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('catalog_product_taxonomy')->insert([
            'product_id' => $productId, 'taxonomy_term_id' => $termId, 'is_protected' => $protected,
            'verification_status' => $verification,
            'evidence_reference' => $verification === 'verified' ? 'private/evidence' : null,
            'verified_by' => $verification === 'verified' ? 'admin' : null,
            'verified_at' => $verification === 'verified' ? now() : null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
