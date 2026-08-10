<?php

namespace App\Modules\Promotions\Domain;

final class EmailCanonicalizer
{
    /**
     * @return array{canonical:string, delivery:string}
     */
    public function canonicalize(string $email): array
    {
        $trimmed = trim($email);
        [$localPart, $domain] = explode('@', $trimmed, 2);

        if (class_exists(\Normalizer::class)) {
            $localPart = \Normalizer::normalize($localPart, \Normalizer::FORM_C) ?: $localPart;
            $domain = \Normalizer::normalize($domain, \Normalizer::FORM_C) ?: $domain;
        }

        $asciiDomain = function_exists('idn_to_ascii')
            ? idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46)
            : $domain;

        $normalizedDomain = mb_strtolower($asciiDomain ?: $domain, 'UTF-8');
        $delivery = $localPart.'@'.$normalizedDomain;
        $canonicalLocalPart = mb_strtolower($localPart, 'UTF-8');

        return [
            'canonical' => $canonicalLocalPart.'@'.$normalizedDomain,
            'delivery' => $delivery,
        ];
    }
}
