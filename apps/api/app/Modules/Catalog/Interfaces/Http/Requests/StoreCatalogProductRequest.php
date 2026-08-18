<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

final class StoreCatalogProductRequest extends CatalogRequest
{
    public function rules(): array
    {
        return CatalogRules::product(false);
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCatalogPayloadValidation($validator);
    }
}
