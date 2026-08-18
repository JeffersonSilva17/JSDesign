<?php

namespace App\Modules\Catalog\Domain;

use InvalidArgumentException;

final readonly class TaxonomyTerm
{
    private const TYPES = ['theme', 'occasion', 'character', 'search_alias'];

    public function __construct(
        public string $label,
        public string $canonicalKey,
        public string $type,
    ) {}

    public static function fromLabel(string $label, string $type): self
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException('Tipo de taxonomia inválido.');
        }

        $normalized = class_exists(\Normalizer::class)
            ? (\Normalizer::normalize($label, \Normalizer::FORM_C) ?: $label)
            : $label;
        $editorial = trim((string) preg_replace('/\s+/u', ' ', $normalized));

        if ($editorial === '') {
            throw new InvalidArgumentException('O rótulo da taxonomia é obrigatório.');
        }

        $lower = mb_strtolower($editorial, 'UTF-8');
        $canonical = strtr($lower, [
            "\u{00E1}" => 'a', "\u{00E0}" => 'a', "\u{00E2}" => 'a', "\u{00E3}" => 'a', "\u{00E4}" => 'a',
            "\u{00E9}" => 'e', "\u{00E8}" => 'e', "\u{00EA}" => 'e', "\u{00EB}" => 'e',
            "\u{00ED}" => 'i', "\u{00EC}" => 'i', "\u{00EE}" => 'i', "\u{00EF}" => 'i',
            "\u{00F3}" => 'o', "\u{00F2}" => 'o', "\u{00F4}" => 'o', "\u{00F5}" => 'o', "\u{00F6}" => 'o',
            "\u{00FA}" => 'u', "\u{00F9}" => 'u', "\u{00FB}" => 'u', "\u{00FC}" => 'u',
            "\u{00E7}" => 'c', "\u{00F1}" => 'n',
        ]);

        return new self($editorial, $canonical, $type);
    }
}
