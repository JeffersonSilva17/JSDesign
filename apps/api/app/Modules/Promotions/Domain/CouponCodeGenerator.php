<?php

namespace App\Modules\Promotions\Domain;

final class CouponCodeGenerator
{
    private const ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    public function generate(): string
    {
        $code = '';
        $max = strlen(self::ALPHABET) - 1;

        for ($index = 0; $index < 16; $index++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return 'JS-'.$code;
    }
}
