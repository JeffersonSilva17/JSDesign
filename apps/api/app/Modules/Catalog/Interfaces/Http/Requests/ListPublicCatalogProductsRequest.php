<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use App\Modules\Catalog\Application\Queries\PublicCatalogFilters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListPublicCatalogProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function filters(): PublicCatalogFilters
    {
        $rawQuery = (string) $this->server('QUERY_STRING', '');
        if ($rawQuery !== '' && (str_contains($rawQuery, ';') || preg_match('/%(?![0-9A-Fa-f]{2})/', $rawQuery) === 1)) {
            throw ValidationException::withMessages(['query' => ['A consulta contém formato inválido.']]);
        }

        $seen = [];
        foreach (array_filter(explode('&', $rawQuery)) as $part) {
            $key = rawurldecode(explode('=', $part, 2)[0]);
            if ($key === '' || str_contains($key, '[') || str_contains($key, ']') || isset($seen[$key])) {
                throw ValidationException::withMessages(['query' => ['A consulta contém chaves repetidas ou em formato inválido.']]);
            }
            $seen[$key] = true;
        }
        try {
            return PublicCatalogFilters::fromArray($this->query->all());
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['query' => [$exception->getMessage()]]);
        }
    }
}
