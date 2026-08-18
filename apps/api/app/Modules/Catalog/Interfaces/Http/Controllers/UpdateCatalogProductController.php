<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\Security\AdminIdentity;
use App\Modules\Catalog\Application\UpdateCatalogProduct;
use App\Modules\Catalog\Interfaces\Http\Requests\UpdateCatalogProductRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\AdminCatalogProductResource;

final readonly class UpdateCatalogProductController
{
    public function __construct(private UpdateCatalogProduct $update) {}

    public function __invoke(UpdateCatalogProductRequest $request, string $product): AdminCatalogProductResource
    {
        /** @var AdminIdentity $identity */
        $identity = $request->attributes->get('catalog_admin_identity');
        $data = $request->validated();
        $version = (int) $data['version'];
        unset($data['version']);

        return new AdminCatalogProductResource($this->update->handle($product, $version, $data, $identity->id));
    }
}
