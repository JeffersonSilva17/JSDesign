<?php

namespace App\Modules\Promotions\Application\Privacy;

use App\Modules\Promotions\Domain\EmailCanonicalizer;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use App\Modules\Promotions\Domain\PromotionCouponRepository;

final readonly class AnonymizeFirstPurchaseCoupon
{
    public function __construct(
        private EmailCanonicalizer $emailCanonicalizer,
        private FirstPurchasePromotionSettings $settings,
        private PromotionCouponRepository $repository,
    ) {}

    public function anonymizeByEmail(string $email, string $reason): int
    {
        $emailData = $this->emailCanonicalizer->canonicalize($email);

        return $this->repository->anonymizeByFingerprints(
            $this->settings->emailFingerprints($emailData['canonical']),
            $reason,
        );
    }
}
