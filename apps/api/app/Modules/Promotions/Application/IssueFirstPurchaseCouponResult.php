<?php

namespace App\Modules\Promotions\Application;

final readonly class IssueFirstPurchaseCouponResult
{
    public function __construct(
        public string $delivery,
        public string $message,
        public string $requestId,
        public ?string $couponCode,
    ) {}
}
