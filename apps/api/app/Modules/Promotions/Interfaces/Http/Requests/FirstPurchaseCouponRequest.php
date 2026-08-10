<?php

namespace App\Modules\Promotions\Interfaces\Http\Requests;

use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class FirstPurchaseCouponRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => trim((string) $this->input('email'))]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'authorization_accepted' => ['accepted'],
            'authorization_text_version' => ['required', 'string', 'max:80'],
            'offer_id' => ['required', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Informe seu e-mail para solicitar o cupom.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail informado é muito longo.',
            'authorization_accepted.accepted' => 'Confirme a autorização específica para solicitar o cupom.',
            'authorization_text_version.required' => 'A versão da autorização é obrigatória.',
            'offer_id.required' => 'A identificação da oferta é obrigatória.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $settings = app(FirstPurchasePromotionSettings::class);

                if ($this->string('authorization_text_version')->toString() !== $settings->authorizationTextVersion()) {
                    $validator->errors()->add('authorization_text_version', 'A versão da autorização não está ativa.');
                }

                if ($this->string('offer_id')->toString() !== $settings->offerId()) {
                    $validator->errors()->add('offer_id', 'A oferta informada não está ativa.');
                }
            },
        ];
    }
}
