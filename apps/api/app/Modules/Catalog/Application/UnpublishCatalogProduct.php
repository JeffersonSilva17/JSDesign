<?php

namespace App\Modules\Catalog\Application;

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\Product;

final readonly class UnpublishCatalogProduct
{
    public function __construct(private CatalogProductRepository $repository) {}

    public function handle(string $id, int $expectedVersion): array
    {
        $current = $this->repository->find($id)
            ?? throw new CatalogConflict('product_not_found', 'Produto não encontrado.');

        if ((int) $current['version'] !== $expectedVersion) {
            throw new CatalogConflict('version_conflict', 'A versao informada esta desatualizada.');
        }

        $unpublished = Product::draft($current)->unpublish()->attributes();

        return $this->repository->update($id, $expectedVersion, [
            'status' => $unpublished['status'],
            'unpublished_at' => new \DateTimeImmutable('now', new \DateTimeZone('UTC')),
        ]);
    }
}
