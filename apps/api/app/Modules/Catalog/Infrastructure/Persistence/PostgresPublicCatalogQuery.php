<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;
use App\Modules\Catalog\Application\Queries\PublicCatalogPage;
use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use App\Modules\Catalog\Application\Queries\PublicCatalogText;
use App\Modules\Catalog\Domain\ProductModality;
use App\Modules\Catalog\Infrastructure\Files\PublicImagePath;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final readonly class PostgresPublicCatalogQuery implements PublicCatalogQuery
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

            $items = $this->hydrate($rows->map(static fn (object $row): array => (array) $row)->all(), false);

            return new PublicCatalogPage(
                $items,
                $currentPage,
                $filters->perPage,
                $lastPage,
                $total,
                $this->filterLabels($filters),
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
    private function filterLabels(PublicCatalogFilters $filters): array
    {
        $labels = [];
        if ($filters->category !== null) {
            $label = DB::table('catalog_categories as c')
                ->join('catalog_products as p', 'p.category_id', '=', 'c.id')
                ->where('p.status', 'published')
                ->where('c.slug', $filters->category)
                ->value('c.label');
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

    private function withStatementTimeout(callable $operation): mixed
    {
        return DB::transaction(function () use ($operation): mixed {
            $milliseconds = max(100, min(30000, (int) config('catalog.public_read_statement_timeout_ms', 3000)));
            DB::select("SELECT set_config('statement_timeout', ?, true)", [$milliseconds.'ms']);

            return $operation();
        }, 1);
    }
}
