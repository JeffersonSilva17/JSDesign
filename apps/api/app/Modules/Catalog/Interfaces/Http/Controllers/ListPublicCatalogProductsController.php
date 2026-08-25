<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Queries\ListPublicCatalogProducts;
use App\Modules\Catalog\Interfaces\Http\Requests\ListPublicCatalogProductsRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogProductCollection;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PDOException;

final readonly class ListPublicCatalogProductsController
{
    public function __construct(private ListPublicCatalogProducts $list) {}

    public function __invoke(ListPublicCatalogProductsRequest $request): JsonResponse
    {
        $filters = $request->filters();
        try {
            $page = $this->list->execute($filters);
        } catch (QueryException|PDOException) {
            return response()->json(['message' => 'Catálogo temporariamente indisponível.'], 503)
                ->header('Cache-Control', 'no-store, private');
        }

        $payload = (new PublicCatalogProductCollection($page, $filters))->resolve($request);

        return response()->json($payload)->header('Cache-Control', 'no-store, private');
    }
}
