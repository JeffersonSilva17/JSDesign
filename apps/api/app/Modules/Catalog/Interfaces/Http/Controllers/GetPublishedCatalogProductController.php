<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Queries\GetPublishedCatalogProduct;
use App\Modules\Catalog\Interfaces\Http\Resources\PublicCatalogProductResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PDOException;

final readonly class GetPublishedCatalogProductController
{
    public function __construct(private GetPublishedCatalogProduct $get) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        if (mb_strlen($slug) > 180 || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) !== 1) {
            return response()->json(['message' => 'Produto não encontrado.'], 404)->header('Cache-Control', 'no-store, private');
        }

        try {
            $product = $this->get->execute($slug);
        } catch (QueryException|PDOException) {
            return response()->json(['message' => 'Catálogo temporariamente indisponível.'], 503)
                ->header('Cache-Control', 'no-store, private');
        }
        if ($product === null) {
            return response()->json(['message' => 'Produto não encontrado.'], 404)->header('Cache-Control', 'no-store, private');
        }

        return response()->json(['data' => (new PublicCatalogProductResource($product))->resolve($request)])
            ->header('Cache-Control', 'no-store, private');
    }
}
