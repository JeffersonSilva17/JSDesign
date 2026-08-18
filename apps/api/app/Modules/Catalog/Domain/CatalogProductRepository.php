<?php

namespace App\Modules\Catalog\Domain;

interface CatalogProductRepository
{
    /** @param array<string, mixed> $product */
    public function create(array $product): array;

    /** @param array<string, mixed> $changes */
    public function update(string $id, int $expectedVersion, array $changes): array;

    /** @return array<string, mixed>|null */
    public function find(string $id): ?array;
}
