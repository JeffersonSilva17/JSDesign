<?php

namespace App\Modules\Catalog\Application\Queries;

use InvalidArgumentException;
use Normalizer;

final readonly class PublicCatalogSearchCriteria
{
    private const ALLOWED_KEYS = ['q', 'page', 'per_page'];

    public function __construct(
        public string $query,
        public string $normalizedQuery,
        public int $page = 1,
        public int $perPage = 12,
    ) {}

    /** @param array<string, mixed> $input */
    public static function fromArray(array $input): self
    {
        if (array_diff(array_keys($input), self::ALLOWED_KEYS) !== []) {
            throw new InvalidArgumentException('A consulta contém parâmetros não permitidos.');
        }
        if (! isset($input['q']) || ! is_string($input['q']) || ! mb_check_encoding($input['q'], 'UTF-8')) {
            throw new InvalidArgumentException('Informe um termo de busca válido.');
        }
        if (preg_match('/[\p{Cc}\p{Cf}]/u', $input['q']) === 1) {
            throw new InvalidArgumentException('O termo contém caracteres não permitidos.');
        }

        $canonical = Normalizer::normalize($input['q'], Normalizer::FORM_KC);
        if (! is_string($canonical)) {
            throw new InvalidArgumentException('Informe um termo de busca válido.');
        }
        $canonical = trim((string) preg_replace('/[\p{Z}\s]+/u', ' ', $canonical));
        if (mb_strlen($canonical) < 2 || mb_strlen($canonical) > 120 || strlen($canonical) > 512) {
            throw new InvalidArgumentException('O termo deve ter entre 2 e 120 caracteres e no máximo 512 bytes.');
        }

        return new self(
            $canonical,
            self::normalize($canonical),
            self::canonicalInteger($input['page'] ?? '1', 1, 1000, 'page'),
            self::canonicalInteger($input['per_page'] ?? '12', 1, 48, 'per_page'),
        );
    }

    public static function normalize(string $value): string
    {
        $normalized = Normalizer::normalize($value, Normalizer::FORM_D);
        if (! is_string($normalized)) {
            return '';
        }
        $normalized = (string) preg_replace('/\p{Mn}+/u', '', $normalized);
        $normalized = mb_strtolower($normalized, 'UTF-8');

        return trim((string) preg_replace('/[\p{Z}\s]+/u', ' ', $normalized));
    }

    private static function canonicalInteger(mixed $value, int $min, int $max, string $field): int
    {
        if (! is_scalar($value) || is_bool($value)) {
            throw new InvalidArgumentException("O parâmetro $field deve ser um inteiro decimal canônico.");
        }
        $string = (string) $value;
        if (preg_match('/^[1-9][0-9]*$/D', $string) !== 1) {
            throw new InvalidArgumentException("O parâmetro $field deve ser um inteiro decimal canônico.");
        }
        $integer = filter_var($string, FILTER_VALIDATE_INT);
        if ($integer === false || $integer < $min || $integer > $max) {
            throw new InvalidArgumentException("O parâmetro $field está fora do limite.");
        }

        return $integer;
    }
}
