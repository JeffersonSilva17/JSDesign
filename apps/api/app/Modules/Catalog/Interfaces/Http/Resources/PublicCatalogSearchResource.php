<?php

namespace App\Modules\Catalog\Interfaces\Http\Resources;

use App\Modules\Catalog\Application\Queries\PublicCatalogSearchPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCatalogSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var PublicCatalogSearchPage $page */
        $page = $this->resource;
        $groups = [];
        foreach ($page->exactGroups as $group) {
            $groups[] = [
                'category' => ['slug' => $group->slug, 'label' => $group->label],
                'items' => array_map(
                    static fn (array $item): array => (new PublicCatalogProductResource($item))->resolve($request),
                    $group->items,
                ),
            ];
        }

        return [
            'data' => [
                'exact_groups' => $groups,
                'similar' => array_map(
                    static fn (array $item): array => (new PublicCatalogProductResource($item))->resolve($request),
                    $page->similar,
                ),
                'suggestions' => array_map(
                    static fn ($suggestion): array => ['label' => $suggestion->label, 'href' => $suggestion->href],
                    $page->suggestions,
                ),
                'intent' => [
                    'type' => $page->intent->type,
                    'preserved_term' => $page->intent->preservedTerm,
                    'handoff_href' => $page->intent->handoffHref,
                ],
            ],
            'meta' => [
                'query' => $page->query,
                'current_page' => $page->currentPage,
                'per_page' => $page->perPage,
                'last_page' => $page->lastPage,
                'total' => $page->total,
                'total_exact' => $page->totalExact,
                'total_similar' => $page->totalSimilar,
            ],
        ];
    }
}
