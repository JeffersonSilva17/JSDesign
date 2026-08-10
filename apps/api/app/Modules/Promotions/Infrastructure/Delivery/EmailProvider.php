<?php

namespace App\Modules\Promotions\Infrastructure\Delivery;

interface EmailProvider
{
    public function queueFirstPurchaseCoupon(int $couponId): void;
}
