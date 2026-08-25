<?php

namespace App\Modules\Catalog\Application\Queries;

final class PublicCatalogText
{
    public static function excerpt(string $value, int $limit): string
    {
        $clean = trim((string) preg_replace('/\s+/u', ' ', $value));
        if (mb_strlen($clean, 'UTF-8') <= $limit) {
            return $clean;
        }

        $candidate = rtrim(mb_substr($clean, 0, $limit - 1, 'UTF-8'));
        $lastSpace = mb_strrpos($candidate, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $candidate = rtrim(mb_substr($candidate, 0, $lastSpace, 'UTF-8'));
        }

        return $candidate.'…';
    }
}
