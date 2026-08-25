<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Queries\GetPublicCatalogFacets;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PDOException;

final readonly class GetPublicCatalogFacetsController
{
    public function __construct(private GetPublicCatalogFacets $facets) {}

    public function __invoke(): JsonResponse
    {
        try {
            $facets = $this->facets->execute();
        } catch (QueryException|PDOException) {
            return response()->json(['message' => 'Catálogo temporariamente indisponível.'], 503)
                ->header('Cache-Control', 'no-store, private');
        }

        return response()->json(['data' => $facets])->header('Cache-Control', 'no-store, private');
    }
}
