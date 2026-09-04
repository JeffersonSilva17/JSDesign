<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use App\Modules\Catalog\Application\Queries\PublicCatalogSearchCriteria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class SearchPublicCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function criteria(): PublicCatalogSearchCriteria
    {
        $rawQuery = (string) $this->server('QUERY_STRING', '');
        if (str_contains($rawQuery, ';') || str_starts_with($rawQuery, '&') || str_ends_with($rawQuery, '&')
            || str_contains($rawQuery, '&&') || preg_match('/%(?![0-9A-Fa-f]{2})/', $rawQuery) === 1) {
            $this->invalid('A consulta contém formato inválido.');
        }

        $seen = [];
        foreach (array_filter(explode('&', $rawQuery), static fn (string $part): bool => $part !== '') as $part) {
            [$rawKey, $rawValue] = array_pad(explode('=', $part, 2), 2, '');
            $key = rawurldecode($rawKey);
            $value = rawurldecode(str_replace('+', ' ', $rawValue));
            if ($key === '' || ! mb_check_encoding($key, 'UTF-8') || ! mb_check_encoding($value, 'UTF-8')
                || str_contains($key, '[') || str_contains($key, ']') || isset($seen[$key])) {
                $this->invalid('A consulta contém chaves repetidas ou formato inválido.');
            }
            $seen[$key] = true;
        }

        try {
            return PublicCatalogSearchCriteria::fromArray($this->query->all());
        } catch (InvalidArgumentException $exception) {
            $this->invalid($exception->getMessage());
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['query' => [$message]]);
    }
}
