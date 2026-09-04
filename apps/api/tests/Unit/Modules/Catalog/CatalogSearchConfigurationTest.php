<?php

namespace Tests\Unit\Modules\Catalog;

use App\Modules\Catalog\Infrastructure\Config\CatalogSearchConfiguration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CatalogSearchConfigurationTest extends TestCase
{
    #[DataProvider('invalidIntegers')]
    public function test_integer_uses_safe_default_for_invalid_values(mixed $value): void
    {
        self::assertSame(60, CatalogSearchConfiguration::integer($value, 60, 10, 120));
    }

    public static function invalidIntegers(): array
    {
        return [[null], ['abc'], ['12.5'], ['9'], ['121'], ['1e2'], [-10]];
    }

    #[DataProvider('invalidDecimals')]
    public function test_decimal_uses_safe_default_for_invalid_values(mixed $value): void
    {
        self::assertSame(0.30, CatalogSearchConfiguration::decimal($value, 0.30, 0.20, 0.80));
    }

    public static function invalidDecimals(): array
    {
        return [[null], ['abc'], ['.30'], ['0.19'], ['0.81'], ['3e-1'], [-0.3]];
    }

    public function test_valid_values_are_preserved(): void
    {
        self::assertSame(120, CatalogSearchConfiguration::integer('120', 60, 10, 120));
        self::assertSame(0.45, CatalogSearchConfiguration::decimal('0.45', 0.30, 0.20, 0.80));
    }
}
