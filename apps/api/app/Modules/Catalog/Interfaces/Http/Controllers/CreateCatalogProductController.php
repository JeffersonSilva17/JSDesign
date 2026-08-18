<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\CreateCatalogProduct;
use App\Modules\Catalog\Application\Security\AdminIdentity;
use App\Modules\Catalog\Interfaces\Http\Requests\StoreCatalogProductRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\AdminCatalogProductResource;
use Illuminate\Http\JsonResponse;

final readonly class CreateCatalogProductController
{
    public function __construct(private CreateCatalogProduct $create) {}

    public function __invoke(StoreCatalogProductRequest $request): JsonResponse
    {
        /** @var AdminIdentity $identity */
        $identity = $request->attributes->get('catalog_admin_identity');
        $resource = new AdminCatalogProductResource($this->create->handle($request->validated(), $identity->id));

        return $resource->response()->setStatusCode(201);
    }
}
