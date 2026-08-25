<?php

namespace Tests\Unit\Modules\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use App\Modules\Catalog\Application\Queries\PublicCatalogText;
use App\Modules\Catalog\Infrastructure\Files\FailClosedPublicCatalogImageResolver;
use App\Modules\Catalog\Infrastructure\Files\PublicImagePath;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PublicCatalogReadTest extends TestCase
{
    public function test_filters_keep_only_the_public_catalog_vocabulary(): void
    {
        $filters = PublicCatalogFilters::fromArray([
            'category' => 'festas-infantis',
            'occasion' => 'aniversario',
            'modality' => 'digital_ready',
            'page' => '2',
            'per_page' => '48',
        ]);

        self::assertSame('festas-infantis', $filters->category);
        self::assertSame('aniversario', $filters->occasion);
        self::assertSame('digital_ready', $filters->modality);
        self::assertSame(2, $filters->page);
        self::assertSame(48, $filters->perPage);
        self::assertSame([
            'category' => 'festas-infantis',
            'occasion' => 'aniversario',
            'modality' => 'digital_ready',
        ], $filters->applied());
    }

    #[DataProvider('invalidFilterProvider')]
    public function test_filters_reject_non_canonical_or_out_of_scope_values(array $input): void
    {
        $this->expectException(InvalidArgumentException::class);
        PublicCatalogFilters::fromArray($input);
    }

    public static function invalidFilterProvider(): array
    {
        return [
            'unknown key' => [['character' => 'princesa']],
            'array value' => [['category' => ['festas']]],
            'blank' => [['category' => ' ']],
            'bad slug' => [['category' => '../segredo']],
            'bad modality' => [['modality' => 'character']],
            'coerced page' => [['page' => '01']],
            'oversized page number' => [['page' => '10001']],
            'oversized page size' => [['per_page' => '49']],
        ];
    }

    public function test_excerpt_is_unicode_safe_and_stops_on_a_word_boundary(): void
    {
        $text = str_repeat('convite ágil ', 30);
        $excerpt = PublicCatalogText::excerpt($text, 40);

        self::assertLessThanOrEqual(40, mb_strlen($excerpt));
        self::assertSame('convite ágil convite ágil convite…', $excerpt);
    }

    #[DataProvider('unsafePathProvider')]
    public function test_public_image_path_rejects_unsafe_or_external_urls(string $path): void
    {
        self::assertNull(PublicImagePath::validate($path));
    }

    public static function unsafePathProvider(): array
    {
        return [
            ['https://cdn.example/image.jpg'],
            ['//cdn.example/image.jpg'],
            ['/media/../secret'],
            ['/media\\image.jpg'],
            ['/media/image.jpg#fragment'],
            ['/media/image.jpg?version=1'],
            ["/media/\nimage.jpg"],
        ];
    }

    public function test_default_image_resolver_fails_closed(): void
    {
        $resolver = new FailClosedPublicCatalogImageResolver;

        self::assertSame([], $resolver->resolveBatch(['opaque-reference']));
    }
}
