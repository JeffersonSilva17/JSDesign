<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;
use App\Modules\Catalog\Application\Queries\PublicCatalogPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchCriteria;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchGroup;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchIntent;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchQuery;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchSuggestion;
use App\Modules\Catalog\Application\Queries\PublicCatalogText;
use App\Modules\Catalog\Domain\ProductModality;
use App\Modules\Catalog\Infrastructure\Files\PublicImagePath;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Normalizer;
use PDOException;

final readonly class PostgresPublicCatalogQuery implements PublicCatalogQuery, PublicCatalogSearchQuery
{
    public function __construct(private PublicCatalogImageResolver $images) {}

    public function list(PublicCatalogFilters $filters): PublicCatalogPage
    {
        return $this->withStatementTimeout(function () use ($filters): PublicCatalogPage {
            $query = $this->publishedQuery($filters);
            $rows = $query
                ->orderByDesc('p.published_at')->orderByDesc('p.id')
                ->offset(($filters->page - 1) * $filters->perPage)->limit($filters->perPage)
                ->get([...$this->productColumns(), DB::raw('count(*) over() as __total_count')]);
            $total = $rows->isEmpty() ? (clone $this->publishedQuery($filters))->count('p.id') : (int) $rows->first()->__total_count;
            $lastPage = max(1, (int) ceil($total / $filters->perPage));
            $currentPage = $total === 0 ? 1 : $filters->page;

            $rawRows = $rows->map(static fn (object $row): array => (array) $row)->all();
            $items = $this->hydrate($rawRows, false);

            return new PublicCatalogPage(
                $items,
                $currentPage,
                $filters->perPage,
                $lastPage,
                $total,
                $this->filterLabels($filters, $rawRows),
            );
        });
    }

    public function facets(): array
    {
        return $this->withStatementTimeout(function (): array {
            $categories = DB::table('catalog_categories as c')->join('catalog_products as p', 'p.category_id', '=', 'c.id')
                ->where('p.status', 'published')->distinct()->orderBy('c.label')->orderBy('c.slug')
                ->get(['c.slug', 'c.label'])->map(static fn (object $row): array => (array) $row)->all();
            $occasions = DB::table('catalog_taxonomy_terms as t')
                ->join('catalog_product_taxonomy as pt', 'pt.taxonomy_term_id', '=', 't.id')
                ->join('catalog_products as p', 'p.id', '=', 'pt.product_id')
                ->where('p.status', 'published')->where('t.type', 'occasion')->where('pt.is_protected', false)
                ->distinct()->orderBy('t.label')->orderBy('t.canonical_key')
                ->get(['t.canonical_key as key', 't.label'])->map(static fn (object $row): array => (array) $row)->all();
            $available = DB::table('catalog_products')->where('status', 'published')->distinct()->pluck('modality')->all();
            $labels = $this->modalityLabels();
            $modalities = [];
            foreach (ProductModality::cases() as $modality) {
                if (in_array($modality->value, $available, true)) {
                    $modalities[] = ['value' => $modality->value, 'label' => $labels[$modality->value]];
                }
            }

            return compact('categories', 'occasions', 'modalities');
        });
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->withStatementTimeout(function () use ($slug): ?array {
            $row = $this->publishedQuery(new PublicCatalogFilters)
                ->where('p.slug', $slug)->first($this->productColumns());

            if ($row === null) {
                return null;
            }

            return $this->hydrate([(array) $row], true)[0];
        });
    }

    public function search(PublicCatalogSearchCriteria $criteria): PublicCatalogSearchPage
    {
        $page = $this->withStatementTimeout(function () use ($criteria): PublicCatalogSearchPage {
            $threshold = max(0.20, min(0.80, (float) config('catalog.search_similarity_threshold', 0.30)));
            $candidateLimit = max(50, min(2000, (int) config('catalog.search_candidate_limit', 500)));
            DB::select("SELECT set_config('pg_trgm.similarity_threshold', ?, true)", [(string) $threshold]);
            $offset = ($criteria->page - 1) * $criteria->perPage;
            $rows = DB::select($this->searchSql(), [
                $criteria->normalizedQuery,
                $threshold,
                $candidateLimit,
                $offset,
                $criteria->perPage,
            ]);
            $first = $rows[0] ?? null;
            $total = $first === null ? 0 : (int) $first->__total;
            $totalExact = $first === null ? 0 : (int) $first->__total_exact;
            $totalSimilar = $first === null ? 0 : (int) $first->__total_similar;
            $pageRows = array_values(array_filter($rows, static fn (object $row): bool => $row->id !== null));
            $projections = $this->hydrate(array_map(static fn (object $row): array => (array) $row, $pageRows), false);
            $projectionById = [];
            foreach ($projections as $projection) {
                $projectionById[$projection['id']] = $projection;
            }

            $exactGroups = [];
            $similar = [];
            foreach ($pageRows as $row) {
                $projection = $projectionById[(string) $row->id];
                if ((int) $row->__rank <= 1) {
                    $slug = (string) $row->category_slug;
                    if (! isset($exactGroups[$slug])) {
                        $exactGroups[$slug] = [
                            'slug' => $slug,
                            'label' => (string) $row->category_label,
                            'rank' => (int) $row->__rank,
                            'score' => (float) $row->__score,
                            'items' => [],
                        ];
                    }
                    $exactGroups[$slug]['items'][] = $projection;
                } else {
                    $similar[] = $projection;
                }
            }

            $groups = [];
            uasort($exactGroups, static function (array $left, array $right): int {
                return $left['rank'] <=> $right['rank']
                    ?: $right['score'] <=> $left['score']
                    ?: strcmp($left['label'], $right['label'])
                    ?: strcmp($left['slug'], $right['slug']);
            });
            foreach ($exactGroups as $slug => $group) {
                $groups[] = new PublicCatalogSearchGroup($slug, $group['label'], $group['items']);
            }

            return new PublicCatalogSearchPage(
                $groups,
                $similar,
                [],
                $this->searchIntent($criteria, $total),
                $criteria->query,
                $criteria->page,
                $criteria->perPage,
                max(1, (int) ceil($total / $criteria->perPage)),
                $total,
                $totalExact,
                $totalSimilar,
            );
        }, 'catalog.search_statement_timeout_ms');

        try {
            $suggestions = $this->withStatementTimeout(
                fn (): array => $this->searchSuggestions($criteria->normalizedQuery),
                'catalog.search_statement_timeout_ms',
            );
        } catch (QueryException|PDOException) {
            return $page;
        }

        return new PublicCatalogSearchPage(
            $page->exactGroups,
            $page->similar,
            $suggestions,
            $page->intent,
            $page->query,
            $page->currentPage,
            $page->perPage,
            $page->lastPage,
            $page->total,
            $page->totalExact,
            $page->totalSimilar,
        );
    }

    private function searchSql(): string
    {
        return <<<'SQL'
            WITH params AS NOT MATERIALIZED (
                SELECT ?::text AS query, ?::real AS threshold, ?::integer AS candidate_limit,
                       ?::integer AS page_offset, ?::integer AS page_limit
            ), match_values AS (
                SELECT p.id AS product_id, 'name'::text AS source, catalog_public_search_normalize_v1(p.name) AS value
                FROM catalog_products p WHERE p.status = 'published'
                UNION ALL
                SELECT p.id, 'category', catalog_public_search_normalize_v1(c.label)
                FROM catalog_products p JOIN catalog_categories c ON c.id = p.category_id WHERE p.status = 'published'
                UNION ALL
                SELECT p.id, 'modality', catalog_public_search_normalize_v1(CASE p.modality
                    WHEN 'physical_personalized' THEN 'Produto físico personalizado'
                    WHEN 'digital_personalized' THEN 'Produto digital personalizado'
                    WHEN 'digital_ready' THEN 'Produto digital' END)
                FROM catalog_products p WHERE p.status = 'published'
                UNION ALL
                SELECT p.id, t.type, catalog_public_search_normalize_v1(t.label)
                FROM catalog_products p
                JOIN catalog_product_taxonomy pt ON pt.product_id = p.id
                JOIN catalog_taxonomy_terms t ON t.id = pt.taxonomy_term_id
                WHERE p.status = 'published' AND (
                    (t.type IN ('theme', 'occasion', 'search_alias') AND pt.is_protected = false)
                    OR (t.type = 'character' AND pt.is_protected = true AND pt.verification_status = 'verified')
                )
            ), non_fuzzy_scored AS (
                SELECT mv.*, CASE
                    WHEN mv.value = params.query AND mv.source = 'name' THEN 0
                    WHEN mv.value = params.query THEN 1
                    ELSE 2 END AS rank,
                    CASE
                    WHEN mv.value = params.query THEN 1.0
                    ELSE length(params.query)::real / greatest(length(mv.value), 1) END AS score
                FROM match_values mv CROSS JOIN params
                WHERE mv.value = params.query
                   OR left(mv.value, length(params.query)) = params.query
                   OR position(' ' || params.query IN mv.value) > 0
            ), non_fuzzy_best AS (
                SELECT DISTINCT ON (product_id) * FROM non_fuzzy_scored
                ORDER BY product_id, rank, score DESC, source
            ), non_fuzzy AS (
                SELECT non_fuzzy_best.* FROM non_fuzzy_best
            ), fuzzy_match_values AS (
                SELECT p.id AS product_id, 'name'::text AS source, catalog_public_search_normalize_v1(p.name) AS value
                FROM catalog_products p CROSS JOIN params
                WHERE p.status = 'published' AND catalog_public_search_normalize_v1(p.name) % params.query
                UNION ALL
                SELECT p.id, 'category', catalog_public_search_normalize_v1(c.label)
                FROM catalog_categories c CROSS JOIN params
                JOIN catalog_products p ON p.category_id = c.id
                WHERE p.status = 'published' AND catalog_public_search_normalize_v1(c.label) % params.query
                UNION ALL
                SELECT p.id, 'modality', catalog_public_search_normalize_v1(CASE p.modality
                    WHEN 'physical_personalized' THEN 'Produto físico personalizado'
                    WHEN 'digital_personalized' THEN 'Produto digital personalizado'
                    WHEN 'digital_ready' THEN 'Produto digital' END)
                FROM catalog_products p CROSS JOIN params
                WHERE p.status = 'published' AND catalog_public_search_normalize_v1(CASE p.modality
                    WHEN 'physical_personalized' THEN 'Produto físico personalizado'
                    WHEN 'digital_personalized' THEN 'Produto digital personalizado'
                    WHEN 'digital_ready' THEN 'Produto digital' END) % params.query
                UNION ALL
                SELECT p.id, t.type, catalog_public_search_normalize_v1(t.label)
                FROM catalog_taxonomy_terms t CROSS JOIN params
                JOIN catalog_product_taxonomy pt ON pt.taxonomy_term_id = t.id
                JOIN catalog_products p ON p.id = pt.product_id
                WHERE p.status = 'published' AND catalog_public_search_normalize_v1(t.label) % params.query AND (
                    (t.type IN ('theme', 'occasion', 'search_alias') AND pt.is_protected = false)
                    OR (t.type = 'character' AND pt.is_protected = true AND pt.verification_status = 'verified')
                )
            ), fuzzy_scored AS (
                SELECT fmv.*, 3 AS rank, similarity(fmv.value, params.query) AS score
                FROM fuzzy_match_values fmv CROSS JOIN params
            ), fuzzy_best AS (
                SELECT DISTINCT ON (product_id) * FROM fuzzy_scored
                ORDER BY product_id, score DESC, source
            ), fuzzy AS (
                SELECT fuzzy_best.* FROM fuzzy_best
                JOIN catalog_products p ON p.id = fuzzy_best.product_id
                CROSS JOIN params
                ORDER BY score DESC, p.published_at DESC, product_id DESC LIMIT (SELECT candidate_limit FROM params)
            ), eligible AS (
                SELECT * FROM non_fuzzy UNION ALL SELECT * FROM fuzzy
            ), best AS (
                SELECT DISTINCT ON (product_id) product_id, rank, score
                FROM eligible ORDER BY product_id, rank, score DESC, source
            ), ranked AS (
                SELECT p.id, p.slug, p.name, p.description, p.modality, p.price_minor, p.currency,
                       p.availability, p.delivery_type, p.production_lead_time_days, p.is_immediate_delivery,
                       p.compatibility, p.published_at, c.slug AS category_slug, c.label AS category_label,
                       best.rank AS __rank, best.score AS __score
                FROM best JOIN catalog_products p ON p.id = best.product_id
                JOIN catalog_categories c ON c.id = p.category_id
                WHERE p.status = 'published'
            ), meta AS (
                SELECT count(*) AS total,
                       count(*) FILTER (WHERE __rank <= 1) AS total_exact,
                       count(*) FILTER (WHERE __rank >= 2) AS total_similar
                FROM ranked
            ), page_rows AS (
                SELECT ranked.* FROM ranked CROSS JOIN params
                ORDER BY __rank, __score DESC, published_at DESC, id DESC
                OFFSET (SELECT page_offset FROM params) LIMIT (SELECT page_limit FROM params)
            )
            SELECT page_rows.*, meta.total AS __total, meta.total_exact AS __total_exact,
                   meta.total_similar AS __total_similar
            FROM meta LEFT JOIN page_rows ON true
            ORDER BY page_rows.__rank, page_rows.__score DESC, page_rows.published_at DESC, page_rows.id DESC
        SQL;
    }

    /** @return list<PublicCatalogSearchSuggestion> */
    private function searchSuggestions(string $query): array
    {
        $rows = DB::select(<<<'SQL'
            WITH editorial AS (
                SELECT c.label, c.slug AS key
                FROM catalog_categories c JOIN catalog_products p ON p.category_id = c.id
                WHERE p.status = 'published'
                UNION
                SELECT t.label, t.canonical_key
                FROM catalog_taxonomy_terms t
                JOIN catalog_product_taxonomy pt ON pt.taxonomy_term_id = t.id
                JOIN catalog_products p ON p.id = pt.product_id
                WHERE p.status = 'published' AND pt.is_protected = false AND t.type IN ('theme', 'occasion')
            )
            SELECT label FROM (
                SELECT DISTINCT ON (catalog_public_search_normalize_v1(label)) label,
                       CASE
                         WHEN catalog_public_search_normalize_v1(label) = ?::text THEN 2.0
                         WHEN left(catalog_public_search_normalize_v1(label), length(?::text)) = ?::text THEN 1.5
                         ELSE similarity(catalog_public_search_normalize_v1(label), ?::text)
                       END AS score,
                       catalog_public_search_normalize_v1(label) AS normalized_label
                FROM editorial
                ORDER BY catalog_public_search_normalize_v1(label), score DESC, label
            ) ranked_suggestions
            ORDER BY score DESC, normalized_label, label
            LIMIT 6
        SQL, [$query, $query, $query, $query]);
        $suggestions = [];
        foreach ($rows as $row) {
            $label = $this->canonicalSuggestionLabel((string) $row->label);
            if ($label !== null) {
                $suggestions[] = new PublicCatalogSearchSuggestion($label, '/buscar?q='.rawurlencode($label));
            }
        }

        return $suggestions;
    }

    private function searchIntent(PublicCatalogSearchCriteria $criteria, int $total): PublicCatalogSearchIntent
    {
        $invitation = false;
        if ($total === 0) {
            foreach ((array) config('catalog.search_invitation_vocabulary', ['convite', 'convites']) as $term) {
                $normalized = PublicCatalogSearchCriteria::normalize((string) $term);
                if ($normalized !== '' && preg_match('/(?:^|\s)'.preg_quote($normalized, '/').'(?:\s|$)/u', $criteria->normalizedQuery) === 1) {
                    $invitation = true;
                    break;
                }
            }
        }

        return new PublicCatalogSearchIntent(
            $invitation ? 'invitation' : 'generic',
            $criteria->query,
            $invitation ? '/produtos?modality=digital_personalized#busca='.rawurlencode($criteria->query) : null,
        );
    }

    private function publishedQuery(PublicCatalogFilters $filters): Builder
    {
        $query = DB::table('catalog_products as p')->join('catalog_categories as c', 'c.id', '=', 'p.category_id')
            ->where('p.status', 'published');

        if ($filters->category !== null) {
            $query->where('c.slug', $filters->category);
        }
        if ($filters->modality !== null) {
            $query->where('p.modality', $filters->modality);
        }
        if ($filters->occasion !== null) {
            $query->whereExists(function (Builder $occasion) use ($filters): void {
                $occasion->selectRaw('1')->from('catalog_product_taxonomy as fpt')
                    ->join('catalog_taxonomy_terms as ft', 'ft.id', '=', 'fpt.taxonomy_term_id')
                    ->whereColumn('fpt.product_id', 'p.id')->where('fpt.is_protected', false)
                    ->where('ft.type', 'occasion')->where('ft.canonical_key', $filters->occasion);
            });
        }

        return $query;
    }

    /** @return list<string> */
    private function productColumns(): array
    {
        return [
            'p.id', 'p.slug', 'p.name', 'p.description', 'p.modality', 'p.price_minor', 'p.currency',
            'p.availability', 'p.delivery_type', 'p.production_lead_time_days', 'p.is_immediate_delivery',
            'p.compatibility', 'c.slug as category_slug', 'c.label as category_label',
        ];
    }

    /** @param list<array<string, mixed>> $rows @return list<array<string, mixed>> */
    private function hydrate(array $rows, bool $detail): array
    {
        if ($rows === []) {
            return [];
        }

        $ids = array_column($rows, 'id');
        $imageRows = DB::table('catalog_product_images')->whereIn('product_id', $ids)->where('is_primary', true)
            ->orderBy('sort_order')->get(['product_id', 'storage_reference', 'alt_text']);
        $references = $imageRows->pluck('storage_reference')->map(static fn (mixed $value): string => (string) $value)->all();
        $resolved = $this->images->resolveBatch($references);
        $primaryByProduct = [];
        foreach ($imageRows as $image) {
            $url = is_string($resolved[$image->storage_reference] ?? null)
                ? PublicImagePath::validate($resolved[$image->storage_reference])
                : null;
            if ($url !== null) {
                $primaryByProduct[$image->product_id] = ['url' => $url, 'alt_text' => (string) $image->alt_text];
            }
        }

        $taxonomyByProduct = [];
        $terms = DB::table('catalog_product_taxonomy as pt')
            ->join('catalog_taxonomy_terms as t', 't.id', '=', 'pt.taxonomy_term_id')
            ->whereIn('pt.product_id', $ids)->where('pt.is_protected', false)
            ->whereIn('t.type', ['theme', 'occasion'])
            ->orderBy('t.type')->orderBy('t.label')->orderBy('t.canonical_key')
            ->get(['pt.product_id', 't.type', 't.canonical_key', 't.label']);
        foreach ($terms as $term) {
            $key = $term->type.'|'.$term->canonical_key;
            $taxonomyByProduct[$term->product_id][$key] = [
                'type' => $term->type, 'key' => $term->canonical_key, 'label' => $term->label,
            ];
        }

        return array_map(function (array $row) use ($detail, $primaryByProduct, $taxonomyByProduct): array {
            $projection = [
                'id' => (string) $row['id'], 'slug' => (string) $row['slug'], 'name' => (string) $row['name'],
                'category' => ['slug' => (string) $row['category_slug'], 'label' => (string) $row['category_label']],
                'modality' => (string) $row['modality'], 'price_minor' => (int) $row['price_minor'],
                'currency' => (string) $row['currency'], 'availability' => (string) $row['availability'],
                'delivery_type' => (string) $row['delivery_type'],
                'production_lead_time_days' => $row['production_lead_time_days'] === null ? null : (int) $row['production_lead_time_days'],
                'is_immediate_delivery' => (bool) $row['is_immediate_delivery'],
                'primary_image' => $primaryByProduct[$row['id']] ?? null,
                'taxonomy' => array_values($taxonomyByProduct[$row['id']] ?? []),
            ];
            if ($detail) {
                $projection['description'] = (string) $row['description'];
                $projection['compatibility'] = $row['compatibility'] === null ? null : (string) $row['compatibility'];
            } else {
                $projection['description_excerpt'] = PublicCatalogText::excerpt((string) $row['description'], 240);
                $projection['compatibility_excerpt'] = $row['compatibility'] === null ? null : PublicCatalogText::excerpt((string) $row['compatibility'], 120);
            }

            return $projection;
        }, $rows);
    }

    /** @return array<string, string> */
    /** @param list<array<string, mixed>> $rows */
    private function filterLabels(PublicCatalogFilters $filters, array $rows): array
    {
        $labels = [];
        if ($filters->category !== null) {
            $label = $rows[0]['category_label'] ?? null;
            if (! is_string($label)) {
                $label = DB::table('catalog_categories as c')
                    ->join('catalog_products as p', 'p.category_id', '=', 'c.id')
                    ->where('p.status', 'published')
                    ->where('c.slug', $filters->category)
                    ->value('c.label');
            }
            if (is_string($label)) {
                $labels['category'] = $label;
            }
        }
        if ($filters->occasion !== null) {
            $label = DB::table('catalog_taxonomy_terms as t')
                ->join('catalog_product_taxonomy as pt', 'pt.taxonomy_term_id', '=', 't.id')
                ->join('catalog_products as p', 'p.id', '=', 'pt.product_id')
                ->where('p.status', 'published')
                ->where('pt.is_protected', false)
                ->where('t.type', 'occasion')
                ->where('t.canonical_key', $filters->occasion)
                ->value('t.label');
            if (is_string($label)) {
                $labels['occasion'] = $label;
            }
        }
        if ($filters->modality !== null) {
            $labels['modality'] = $this->modalityLabels()[$filters->modality];
        }

        return $labels;
    }

    /** @return array<string, string> */
    private function modalityLabels(): array
    {
        return [
            'physical_personalized' => 'Produto físico personalizado',
            'digital_personalized' => 'Produto digital personalizado',
            'digital_ready' => 'Produto digital',
        ];
    }

    private function withStatementTimeout(callable $operation, string $configKey = 'catalog.public_read_statement_timeout_ms'): mixed
    {
        return DB::transaction(function () use ($operation, $configKey): mixed {
            $maximum = $configKey === 'catalog.search_statement_timeout_ms' ? 5000 : 30000;
            $milliseconds = max(100, min($maximum, (int) config($configKey, 3000)));
            DB::select("SELECT set_config('statement_timeout', ?, true)", [$milliseconds.'ms']);

            return $operation();
        }, 1);
    }

    private function canonicalSuggestionLabel(string $label): ?string
    {
        if (preg_match('/[\p{Cc}\p{Cf}]/u', $label) === 1) {
            return null;
        }
        $canonical = Normalizer::normalize($label, Normalizer::FORM_KC);
        if (! is_string($canonical)) {
            return null;
        }
        $canonical = trim((string) preg_replace('/[\p{Z}\s]+/u', ' ', $canonical));
        if (mb_strlen($canonical) < 2 || mb_strlen($canonical) > 120 || strlen($canonical) > 512) {
            return null;
        }

        return $canonical;
    }
}
