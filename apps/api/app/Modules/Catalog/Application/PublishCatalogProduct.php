<?php

namespace App\Modules\Catalog\Application;

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\Product;

final readonly class PublishCatalogProduct
{
    public function __construct(
        private CatalogProductRepository $repository,
        private FileReferenceValidator $files,
    ) {}

    public function handle(string $id, int $expectedVersion): array
    {
        $current = $this->repository->find($id)
            ?? throw new CatalogConflict('product_not_found', 'Produto não encontrado.');

        if ((int) $current['version'] !== $expectedVersion) {
            throw new CatalogConflict('version_conflict', 'A versao informada esta desatualizada.');
        }

        $current['images'] = array_map(function (array $image): array {
            $image['validated'] = $this->files->status((string) ($image['storage_reference'] ?? ''))
                === FileReferenceStatus::Accepted;

            return $image;
        }, $current['images'] ?? []);

        $published = Product::draft($current)->publish()->attributes();

        return $this->repository->update($id, $expectedVersion, [
            'status' => $published['status'],
            'published_at' => new \DateTimeImmutable('now', new \DateTimeZone('UTC')),
            'unpublished_at' => null,
        ]);
    }
}
