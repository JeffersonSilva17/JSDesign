<?php

namespace App\Modules\Promotions\Domain;

interface PromotionCouponRepository
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function issueOrFind(array $data): array;

    /**
     * @param  array<string, string>  $fingerprints
     */
    public function anonymizeByFingerprints(array $fingerprints, string $reason): int;

    /** @param array<string, string> $fingerprints */
    public function exportByFingerprints(array $fingerprints): array;

    /** @param array<string, string> $fingerprints */
    public function revokeByFingerprints(array $fingerprints, string $reason): int;

    /** @param array<string, string> $fingerprints */
    public function suppressByFingerprints(array $fingerprints, string $reason): int;

    /** @param array<string, string> $fingerprints */
    public function deleteByFingerprints(array $fingerprints): int;

    public function anonymizeRetainedBefore(\DateTimeInterface $cutoff, string $reason): int;
}
