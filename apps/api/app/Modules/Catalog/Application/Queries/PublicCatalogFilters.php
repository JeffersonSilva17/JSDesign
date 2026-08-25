<?php

namespace App\Modules\Catalog\Application\Queries;

use App\Modules\Catalog\Domain\ProductModality;
use App\Modules\Catalog\Domain\TaxonomyTerm;
use InvalidArgumentException;

final readonly class PublicCatalogFilters
{
    private const ALLOWED_KEYS = ['category', 'occasion', 'modality', 'page', 'per_page'];

    private const MAX_PAGE = 10000;

    public function __construct(
        public ?string $category = null,
        public ?string $occasion = null,
        public ?string $modality = null,
        public int $page = 1,
        public int $perPage = 12,
    ) {}

    /** @param array<string, mixed> $input */
    public static function fromArray(array $input): self
    {
        if (array_diff(array_keys($input), self::ALLOWED_KEYS) !== []) {
            throw new InvalidArgumentException('A consulta contém filtros não permitidos.');
        }

        foreach ($input as $value) {
            if (! is_scalar($value) || is_bool($value) || trim((string) $value) === '') {
                throw new InvalidArgumentException('Os filtros devem ser valores únicos e não vazios.');
            }
        }

        $category = self::optionalPattern($input, 'category', 160, '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D');
        $occasion = self::optionalOccasion($input);
        $modality = isset($input['modality']) ? (string) $input['modality'] : null;

        if ($modality !== null && ProductModality::tryFrom($modality) === null) {
            throw new InvalidArgumentException('A modalidade informada é inválida.');
        }

        $page = self::canonicalInteger($input['page'] ?? '1', 1, self::MAX_PAGE, 'page');
        $perPage = self::canonicalInteger($input['per_page'] ?? '12', 1, 48, 'per_page');

        return new self($category, $occasion, $modality, $page, $perPage);
    }

    /** @return array<string, string> */
    public function applied(): array
    {
        return array_filter([
            'category' => $this->category,
            'occasion' => $this->occasion,
            'modality' => $this->modality,
        ], static fn (?string $value): bool => $value !== null);
    }

    /** @param array<string, mixed> $input */
    private static function optionalPattern(array $input, string $key, int $max, string $pattern): ?string
    {
        if (! isset($input[$key])) {
            return null;
        }

        $value = (string) $input[$key];
        if (mb_strlen($value) > $max || preg_match($pattern, $value) !== 1) {
            throw new InvalidArgumentException("O filtro $key é inválido.");
        }

        return $value;
    }

    private static function canonicalInteger(mixed $value, int $min, int $max, string $field): int
    {
        $string = (string) $value;
        if (preg_match('/^(?:0|[1-9][0-9]*)$/D', $string) !== 1) {
            throw new InvalidArgumentException("O filtro $field deve ser um inteiro decimal canônico.");
        }

        $integer = filter_var($string, FILTER_VALIDATE_INT);
        if ($integer === false || $integer < $min || $integer > $max) {
            throw new InvalidArgumentException("O filtro $field está fora do limite.");
        }

        return $integer;
    }

    /** @param array<string, mixed> $input */
    private static function optionalOccasion(array $input): ?string
    {
        if (! isset($input['occasion'])) {
            return null;
        }

        $value = (string) $input['occasion'];
        if (mb_strlen($value) > 180 || TaxonomyTerm::fromLabel($value, 'occasion')->canonicalKey !== $value) {
            throw new InvalidArgumentException('O filtro occasion é inválido.');
        }

        return $value;
    }
}
