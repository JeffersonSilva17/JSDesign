<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

final class CatalogSitemapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function page(): int
    {
        $raw = (string) $this->server('QUERY_STRING', '');
        if (! preg_match('/^page=([1-9][0-9]{0,4})$/D', $raw, $matches) || (int) $matches[1] > 10000) {
            throw ValidationException::withMessages(['query' => ['Parâmetro de sitemap inválido.']]);
        }

        return (int) $matches[1];
    }

    public function withoutQuery(): void
    {
        if ((string) $this->server('QUERY_STRING', '') !== '') {
            throw ValidationException::withMessages(['query' => ['Parâmetro de sitemap inválido.']]);
        }
    }
}
