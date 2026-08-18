<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

final class ProductVersionRequest extends CatalogRequest
{
    public function rules(): array
    {
        return ['version' => ['required', 'integer', 'min:1']];
    }
}
