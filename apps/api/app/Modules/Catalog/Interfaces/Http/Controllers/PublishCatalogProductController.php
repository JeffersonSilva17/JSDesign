<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Modules\Catalog\Application\PublishCatalogProduct;
use App\Modules\Catalog\Interfaces\Http\Requests\ProductVersionRequest;
use App\Modules\Catalog\Interfaces\Http\Resources\AdminCatalogProductResource;

final readonly class PublishCatalogProductController
{
    public function __construct(private PublishCatalogProduct $publish) {}

    public function __invoke(ProductVersionRequest $request, string $product): AdminCatalogProductResource
    {
        return new AdminCatalogProductResource(
            $this->publish->handle($product, (int) $request->validated('version')),
        );
    }
}
