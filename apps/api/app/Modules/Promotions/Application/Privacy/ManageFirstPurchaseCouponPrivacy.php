<?php

namespace App\Modules\Promotions\Application\Privacy;

use App\Modules\Promotions\Domain\EmailCanonicalizer;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use App\Modules\Promotions\Domain\PromotionCouponRepository;
use DateTimeInterface;

final readonly class ManageFirstPurchaseCouponPrivacy
{
    public function __construct(
        private EmailCanonicalizer $emailCanonicalizer,
        private FirstPurchasePromotionSettings $settings,
        private PromotionCouponRepository $repository,
    ) {}

    public function exportByEmail(string $email): array
    {
        return $this->repository->exportByFingerprints($this->fingerprints($email));
    }

    public function revokeByEmail(string $email, string $reason): int
    {
        return $this->repository->revokeByFingerprints($this->fingerprints($email), $reason);
    }

    public function suppressByEmail(string $email, string $reason): int
    {
        return $this->repository->suppressByFingerprints($this->fingerprints($email), $reason);
    }

    public function deleteByEmail(string $email): int
    {
        return $this->repository->deleteByFingerprints($this->fingerprints($email));
    }

    public function applyRetention(DateTimeInterface $cutoff, string $reason): int
    {
        return $this->repository->anonymizeRetainedBefore($cutoff, $reason);
    }

    private function fingerprints(string $email): array
    {
        $canonical = $this->emailCanonicalizer->canonicalize($email)['canonical'];

        return $this->settings->emailFingerprints($canonical);
    }
}
