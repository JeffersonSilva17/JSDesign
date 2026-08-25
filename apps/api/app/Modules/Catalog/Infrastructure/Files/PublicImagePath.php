<?php

namespace App\Modules\Catalog\Infrastructure\Files;

final class PublicImagePath
{
    public static function validate(string $path): ?string
    {
        if ($path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        if (preg_match('/[\\x00-\\x1F\\x7F\\\\#?]/u', $path) === 1 || str_contains(rawurldecode($path), '..')) {
            return null;
        }

        if (parse_url($path, PHP_URL_SCHEME) !== null || parse_url($path, PHP_URL_HOST) !== null) {
            return null;
        }

        return $path;
    }
}
