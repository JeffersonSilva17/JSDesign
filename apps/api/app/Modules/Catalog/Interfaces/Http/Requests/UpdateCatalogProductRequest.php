<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

final class UpdateCatalogProductRequest extends CatalogRequest
{
    public function rules(): array
    {
        return ['version' => ['required', 'integer', 'min:1'], ...CatalogRules::product(true)];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCatalogPayloadValidation($validator);
    }
}
