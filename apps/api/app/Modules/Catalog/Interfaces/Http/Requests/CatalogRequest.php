<?php

namespace App\Modules\Catalog\Interfaces\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class CatalogRequest extends FormRequest
{
    protected function addCatalogPayloadValidation(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validatePrimaryImages($validator);
            $this->validateVerifiedEvidence($validator);
            $this->validateStrictIntegers($validator);
        });
    }

    protected function failedValidation(Validator $validator): never
    {
        $errors = [];

        foreach ($validator->errors()->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'code' => 'invalid_field',
                    'message_key' => 'catalog.validation.invalid_field',
                    'field_path' => $field,
                    'recoverable' => true,
                    'detail' => $message,
                ];
            }
        }

        throw new HttpResponseException(response()->json(['errors' => $errors], 422));
    }

    private function validatePrimaryImages(Validator $validator): void
    {
        $images = $this->input('images');

        if (! is_array($images)) {
            return;
        }

        $primaryCount = 0;
        foreach ($images as $image) {
            if (is_array($image) && ($image['is_primary'] ?? false) === true) {
                $primaryCount++;
            }
        }

        if ($primaryCount > 1) {
            $validator->errors()->add('images', 'Apenas uma imagem principal pode ser enviada.');
        }
    }

    private function validateVerifiedEvidence(Validator $validator): void
    {
        $assets = $this->input('protected_assets');

        if (! is_array($assets)) {
            return;
        }

        foreach ($assets as $index => $asset) {
            if (! is_array($asset) || ($asset['status'] ?? null) !== 'verified') {
                continue;
            }

            if (trim((string) ($asset['evidence_reference'] ?? '')) === '') {
                $validator->errors()->add("protected_assets.$index.evidence_reference", 'A evidencia e obrigatoria para direitos verificados.');
            }
        }
    }

    private function validateStrictIntegers(Validator $validator): void
    {
        foreach (['price_minor'] as $field) {
            if (! $this->has($field) || $this->input($field) === null) {
                continue;
            }

            if (! is_int($this->input($field))) {
                $validator->errors()->add($field, 'O campo deve ser um inteiro JSON, nao uma string numerica.');
            }
        }
    }
}
