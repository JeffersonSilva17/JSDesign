<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\UnpublishCatalogProduct;
use App\Modules\Catalog\Interfaces\Http\Requests\ProductVersionRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\AdminCatalogProductResource;

final readonly class UnpublishCatalogProductController
{
    public function __construct(private UnpublishCatalogProduct $unpublish) {}

    public function __invoke(ProductVersionRequest $request, string $product): AdminCatalogProductResource
    {
        return new AdminCatalogProductResource(
            $this->unpublish->handle($product, (int) $request->validated('version')),
        );
    }
}
