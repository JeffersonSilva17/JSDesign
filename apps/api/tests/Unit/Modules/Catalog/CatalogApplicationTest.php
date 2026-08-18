<?php

namespace Tests\Unit\Modules\Catalog;

use App\Modules\Catalog\Application\CreateCatalogProduct;
use App\Modules\Catalog\Application\FileReferenceStatus;
use App\Modules\Catalog\Application\FileReferenceValidator;
use App\Modules\Catalog\Application\IdGenerator;
use App\Modules\Catalog\Application\PublishCatalogProduct;
use App\Modules\Catalog\Application\UpdateCatalogProduct;
use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Domain\CatalogValidationFailed;
use PHPUnit\Framework\TestCase;

final class CatalogApplicationTest extends TestCase
{
    public function test_create_draft_assigns_opaque_id_and_keeps_incomplete_data(): void
    {
        $repository = new InMemoryCatalogRepository;
        $result = (new CreateCatalogProduct($repository, new FixedIdGenerator))->handle([
            'slug' => 'rascunho',
            'name' => 'Rascunho',
            'modality' => 'digital_ready',
        ]);

        self::assertSame(FixedIdGenerator::ID, $result['id']);
        self::assertSame('draft', $result['status']);
        self::assertSame(1, $result['version']);
    }

    public function test_update_rejects_stale_version(): void
    {
        $repository = new InMemoryCatalogRepository;
        $repository->create($this->validProduct());
        $useCase = new UpdateCatalogProduct($repository, new FixedFileReferenceValidator);

        $this->expectException(CatalogConflict::class);
        $useCase->handle(FixedIdGenerator::ID, 8, ['name' => 'Outro nome']);
    }

    public function test_publication_validates_file_reference_and_sets_published_state(): void
    {
        $repository = new InMemoryCatalogRepository;
        $repository->create($this->validProduct());
        $validator = new FakeFileReferenceValidator(['file_01HXYZABCDEF0123456789ABCD']);

        $result = (new PublishCatalogProduct($repository, $validator))->handle(FixedIdGenerator::ID, 1);

        self::assertSame('published', $result['status']);
        self::assertSame(2, $result['version']);
        self::assertSame(['file_01HXYZABCDEF0123456789ABCD'], $validator->checked);
    }

    public function test_publication_rejects_missing_and_rejected_file_references(): void
    {
        foreach ([FileReferenceStatus::Missing, FileReferenceStatus::Rejected] as $status) {
            $repository = new InMemoryCatalogRepository;
            $repository->create($this->validProduct());

            try {
                (new PublishCatalogProduct($repository, new AlwaysFileStatusValidator($status)))
                    ->handle(FixedIdGenerator::ID, 1);
                self::fail('A publicação deveria rejeitar a referência de arquivo.');
            } catch (CatalogValidationFailed $exception) {
                self::assertSame('storage_reference_not_validated', $exception->errors[0]['code']);
            }
        }
    }

    /** @return array<string, mixed> */
    private function validProduct(): array
    {
        return [
            'id' => FixedIdGenerator::ID,
            'slug' => 'convite-digital',
            'name' => 'Convite digital',
            'description' => 'Arquivo pronto',
            'modality' => 'digital_ready',
            'status' => 'draft',
            'category_id' => '019bf1c0-bd64-7000-8000-000000000002',
            'price_minor' => 1000,
            'currency' => 'EUR',
            'availability' => 'available',
            'delivery_type' => 'digital',
            'is_personalized' => false,
            'is_immediate_delivery' => true,
            'requires_briefing' => false,
            'requires_approval' => false,
            'file_description' => 'PDF',
            'compatibility' => 'PDF',
            'usage_terms' => 'Uso pessoal',
            'images' => [[
                'storage_reference' => 'file_01HXYZABCDEF0123456789ABCD',
                'alt_text' => 'Convite',
                'sort_order' => 0,
                'is_primary' => true,
            ]],
            'protected_assets' => [],
            'version' => 1,
        ];
    }
}

final class FixedIdGenerator implements IdGenerator
{
    public const ID = '019bf1c0-bd64-7000-8000-000000000001';

    public function generate(): string
    {
        return self::ID;
    }
}

final class FakeFileReferenceValidator implements FileReferenceValidator
{
    public array $checked = [];

    public function __construct(private readonly array $valid) {}

    public function status(string $reference): FileReferenceStatus
    {
        $this->checked[] = $reference;

        return in_array($reference, $this->valid, true)
            ? FileReferenceStatus::Accepted
            : FileReferenceStatus::Missing;
    }
}

final readonly class AlwaysFileStatusValidator implements FileReferenceValidator
{
    public function __construct(private FileReferenceStatus $result) {}

    public function status(string $reference): FileReferenceStatus
    {
        return $this->result;
    }
}

final class FixedFileReferenceValidator implements FileReferenceValidator
{
    public function status(string $reference): FileReferenceStatus
    {
        return FileReferenceStatus::Accepted;
    }
}

final class InMemoryCatalogRepository implements CatalogProductRepository
{
    private array $products = [];

    public function create(array $product): array
    {
        return $this->products[$product['id']] = $product;
    }

    public function update(string $id, int $expectedVersion, array $changes): array
    {
        $current = $this->products[$id] ?? throw new CatalogConflict('product_not_found', 'Não encontrado.');

        if ($current['version'] !== $expectedVersion) {
            throw new CatalogConflict('version_conflict', 'Versão desatualizada.');
        }

        return $this->products[$id] = array_replace($current, $changes, ['version' => $expectedVersion + 1]);
    }

    public function find(string $id): ?array
    {
        return $this->products[$id] ?? null;
    }
}
