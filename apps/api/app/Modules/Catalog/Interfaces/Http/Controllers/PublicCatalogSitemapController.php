<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Queries\ListPublicCatalogSitemapProducts;
use App\Modules\Catalog\Interfaces\Http\Requests\CatalogSitemapRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogSitemapFacetsResource;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogSitemapResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use OverflowException;
use PDOException;

final readonly class PublicCatalogSitemapController
{
    public function __construct(private ListPublicCatalogSitemapProducts $list) {}

    public function products(CatalogSitemapRequest $request): JsonResponse
    {
        $number = $request->page();
        try {
            $page = $this->list->execute($number);
            if ($number > $page->lastPage()) {
                return response()->json(['message' => 'Lote não encontrado.'], 404);
            }

            return response()->json((new PublicCatalogSitemapResource($page))->resolve($request));
        } catch (QueryException|PDOException|OverflowException) {
            return response()->json(['message' => 'Catálogo temporariamente indisponível.'], 503)->header('Retry-After', '60');
        }
    }

    public function facets(CatalogSitemapRequest $request): JsonResponse
    {
        $request->withoutQuery();
        try {
            return response()->json((new PublicCatalogSitemapFacetsResource($this->list->categories()))->resolve($request));
        } catch (QueryException|PDOException|OverflowException) {
            return response()->json(['message' => 'Catálogo temporariamente indisponível.'], 503)->header('Retry-After', '60');
        }
    }
}
