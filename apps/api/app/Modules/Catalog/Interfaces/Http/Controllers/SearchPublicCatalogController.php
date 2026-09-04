<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Queries\SearchPublicCatalog;
use App\Modules\Catalog\Interfaces\Http\Requests\SearchPublicCatalogRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogSearchResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PDOException;

final readonly class SearchPublicCatalogController
{
    public function __construct(private SearchPublicCatalog $search) {}

    public function __invoke(SearchPublicCatalogRequest $request): JsonResponse
    {
        $criteria = $request->criteria();
        try {
            $page = $this->search->execute($criteria);
        } catch (QueryException|PDOException) {
            return response()->json(['message' => 'Busca temporariamente indisponível.'], 503)
                ->header('Cache-Control', 'no-store, private');
        }

        return response()->json((new PublicCatalogSearchResource($page))->resolve($request))
            ->header('Cache-Control', 'no-store, private');
    }
}
