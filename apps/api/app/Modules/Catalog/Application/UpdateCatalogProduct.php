<?php

namespace App\Modules\Catalog\Application;

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\CatalogValidationFailed;
use App\Modules\Catalog\Domain\Product;
use App\Modules\Catalog\Domain\TaxonomyTerm;

final readonly class UpdateCatalogProduct
{
    public function __construct(
        private CatalogProductRepository $repository,
        private FileReferenceValidator $files,
    ) {}

    /** @param array<string, mixed> $changes */
    public function handle(string $id, int $expectedVersion, array $changes, ?string $actorId = null): array
    {
        $current = $this->repository->find($id)
            ?? throw new CatalogConflict('product_not_found', 'Produto não encontrado.');

        if ((int) $current['version'] !== $expectedVersion) {
            throw new CatalogConflict('version_conflict', 'A versão informada está desatualizada.');
        }

        if (isset($changes['slug']) && $changes['slug'] !== $current['slug']) {
            throw new CatalogValidationFailed([[
                'code' => 'slug_change_requires_explicit_operation',
                'message_key' => 'catalog.validation.slug_change_requires_explicit_operation',
                'field_path' => 'slug',
                'recoverable' => true,
                'next_action' => 'keep_current_slug',
            ]]);
        }

        if (isset($changes['protected_assets'])) {
            $changes['protected_assets'] = $this->protectVerifiedEvidence(
                $current['protected_assets'] ?? [],
                $changes['protected_assets'],
            );
            $changes['protected_assets'] = $this->recordVerifications($changes['protected_assets'], $actorId);
        }
        $product = Product::draft(array_replace($current, $changes));
        if (($current['status'] ?? null) === 'published') {
            $attributes = $product->attributes();
            $attributes['images'] = array_map(function (array $image): array {
                $image['validated'] = $this->files->status((string) ($image['storage_reference'] ?? ''))
                    === FileReferenceStatus::Accepted;

                return $image;
            }, $attributes['images'] ?? []);
            $product = Product::draft($attributes);
            $errors = $product->publicationErrors();

            if ($errors !== []) {
                throw new CatalogValidationFailed($errors);
            }
        }
        $validated = $product->attributes();
        unset($validated['id'], $validated['version'], $validated['created_at'], $validated['updated_at']);

        return $this->repository->update($id, $expectedVersion, $validated);
    }

    /** @param list<array<string, mixed>> $assets */
    private function recordVerifications(array $assets, ?string $actorId): array
    {
        return array_map(static function (array $asset) use ($actorId): array {
            if (($asset['status'] ?? null) === 'verified' && $actorId !== null && empty($asset['verified_by'])) {
                $asset['verified_by'] = $actorId;
                $asset['verified_at'] = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            }

            return $asset;
        }, $assets);
    }

    /** @param list<array<string, mixed>> $current */
    private function protectVerifiedEvidence(array $current, array $replacement): array
    {
        foreach ($current as $asset) {
            if (($asset['status'] ?? null) !== 'verified') {
                continue;
            }

            $matching = array_values(array_filter(
                $replacement,
                static fn (array $candidate): bool => TaxonomyTerm::fromLabel(
                    (string) ($candidate['label'] ?? ''),
                    'character',
                )->canonicalKey === ($asset['canonical_key'] ?? null),
            ));

            if (
                $matching === []
                || ($matching[0]['status'] ?? null) !== 'verified'
                || ($matching[0]['evidence_reference'] ?? null) !== ($asset['evidence_reference'] ?? null)
                || ($matching[0]['notes'] ?? null) !== ($asset['notes'] ?? null)
            ) {
                throw new CatalogValidationFailed([[
                    'code' => 'verified_evidence_is_immutable',
                    'message_key' => 'catalog.validation.verified_evidence_is_immutable',
                    'field_path' => 'protected_assets',
                    'recoverable' => true,
                    'next_action' => 'register_new_verification',
                ]]);
            }

            $index = array_search($matching[0], $replacement, true);
            $replacement[$index]['verified_by'] = $asset['verified_by'];
            $replacement[$index]['verified_at'] = $asset['verified_at'];
        }

        return $replacement;
    }
}
