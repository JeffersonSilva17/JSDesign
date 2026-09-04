<?php

namespace App\Modules\Catalog\Infrastructure\Config;

final class CatalogSearchConfiguration
{
    public static function integer(mixed $raw, int $default, int $minimum, int $maximum): int
    {
        if ($raw === null || preg_match('/^[0-9]+$/D', (string) $raw) !== 1) {
            return $default;
        }
        $value = filter_var($raw, FILTER_VALIDATE_INT);

        return $value !== false && $value >= $minimum && $value <= $maximum ? $value : $default;
    }

    public static function decimal(mixed $raw, float $default, float $minimum, float $maximum): float
    {
        if ($raw === null || preg_match('/^(?:0|[1-9][0-9]*)(?:\.[0-9]+)?$/D', (string) $raw) !== 1) {
            return $default;
        }
        $value = (float) $raw;

        return $value >= $minimum && $value <= $maximum ? $value : $default;
    }
}
