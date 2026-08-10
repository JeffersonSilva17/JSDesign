<?php

namespace Tests\Unit\Modules\Promotions;

use App\Modules\Promotions\Domain\CouponCodeGenerator;
use App\Modules\Promotions\Domain\EmailCanonicalizer;
use PHPUnit\Framework\TestCase;

final class PromotionDomainTest extends TestCase
{
    public function test_email_canonicalization_preserves_local_part_and_lowercases_domain(): void
    {
        $canonicalizer = new EmailCanonicalizer;

        $result = $canonicalizer->canonicalize(' Cliente+Tema@EXEMPLO.COM ');

        self::assertSame('cliente+tema@exemplo.com', $result['canonical']);
        self::assertSame('Cliente+Tema@exemplo.com', $result['delivery']);
    }

    public function test_coupon_code_is_human_readable_and_high_entropy_shape(): void
    {
        $generator = new CouponCodeGenerator;
        $codes = [];

        for ($index = 0; $index < 64; $index++) {
            $code = $generator->generate();

            self::assertMatchesRegularExpression('/^JS-[23456789ABCDEFGHJKLMNPQRSTUVWXYZ]{16}$/', $code);
            $codes[] = $code;
        }

        self::assertCount(64, array_unique($codes));
    }
}
