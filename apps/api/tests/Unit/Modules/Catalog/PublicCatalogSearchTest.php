<?php

namespace Tests\Unit\Modules\Catalog;

use App\Modules\Catalog\Application\Queries\PublicCatalogSearchCriteria;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PublicCatalogSearchTest extends TestCase
{
    public function test_criteria_collapse_spaces_and_preserve_the_canonical_unicode_term(): void
    {
        $criteria = PublicCatalogSearchCriteria::fromArray([
            'q' => "  Convite\u{00A0}  Ágil  ",
            'page' => '2',
            'per_page' => '24',
        ]);

        self::assertSame('Convite Ágil', $criteria->query);
        self::assertSame('convite agil', $criteria->normalizedQuery);
        self::assertSame(2, $criteria->page);
        self::assertSame(24, $criteria->perPage);
    }

    #[DataProvider('invalidCriteriaProvider')]
    public function test_criteria_reject_invalid_or_non_canonical_input(array $input): void
    {
        $this->expectException(InvalidArgumentException::class);

        PublicCatalogSearchCriteria::fromArray($input);
    }

    public static function invalidCriteriaProvider(): array
    {
        return [
            'missing query' => [[]],
            'blank' => [['q' => " \t "]],
            'one character' => [['q' => 'a']],
            'more than 120 unicode characters' => [['q' => str_repeat('á', 121)]],
            'more than 512 utf8 bytes' => [['q' => str_repeat('😀', 129)]],
            'control' => [['q' => "convite\nsecreto"]],
            'zero width' => [['q' => "convi\u{200B}te"]],
            'unknown key' => [['q' => 'convite', 'character' => 'x']],
            'array query' => [['q' => ['convite']]],
            'non canonical page' => [['q' => 'convite', 'page' => '01']],
            'page above limit' => [['q' => 'convite', 'page' => '1001']],
            'page size above limit' => [['q' => 'convite', 'per_page' => '49']],
        ];
    }
}
