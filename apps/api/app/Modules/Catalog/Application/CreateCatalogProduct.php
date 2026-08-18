<?php

namespace App\Modules\Catalog\Application;

use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\Product;

final readonly class CreateCatalogProduct
{
    public function __construct(
        private CatalogProductRepository $repository,
        private IdGenerator $ids,
    ) {}

    /** @param array<string, mixed> $data */
    public function handle(array $data, ?string $actorId = null): array
    {
        $data['protected_assets'] = $this->recordVerifications($data['protected_assets'] ?? [], $actorId);
        $draft = Product::draft([
            ...$data,
            'id' => $this->ids->generate(),
            'status' => 'draft',
            'version' => 1,
        ]);

        return $this->repository->create($draft->attributes());
    }

    /** @param list<array<string, mixed>> $assets */
    private function recordVerifications(array $assets, ?string $actorId): array
    {
        return array_map(static function (array $asset) use ($actorId): array {
            if (($asset['status'] ?? null) === 'verified' && $actorId !== null) {
                $asset['verified_by'] = $actorId;
                $asset['verified_at'] = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            }

            return $asset;
        }, $assets);
    }
}
