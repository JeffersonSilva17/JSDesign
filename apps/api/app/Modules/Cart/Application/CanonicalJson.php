<?php

namespace App\Modules\Cart\Application;

final class CanonicalJson
{
    /** @param array<string,mixed> $value */
    public static function hash(array $value): string
    {
        return hash('sha256', json_encode(self::normalize($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    private static function normalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }
        if (array_is_list($value)) {
            return array_map(self::normalize(...), $value);
        }
        ksort($value, SORT_STRING);
        foreach ($value as $key => $entry) {
            $value[$key] = self::normalize($entry);
        }

        return $value;
    }
}
