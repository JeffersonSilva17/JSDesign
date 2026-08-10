<?php

namespace App\Modules\Promotions\Infrastructure\Persistence;

use App\Modules\Promotions\Domain\PromotionCouponRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class PostgresPromotionCouponRepository implements PromotionCouponRepository
{
    public function issueOrFind(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $campaignId = $this->activeCampaignId($data);
            $existing = $this->findActiveCoupon($data['email_fingerprints'], true);

            if ($existing) {
                DB::table('promotion_coupons')
                    ->where('id', $existing->id)
                    ->update([
                        'authorization_text_version' => $data['authorization_text_version'],
                        'authorization_accepted_at' => $data['authorization_accepted_at'],
                        'legal_basis' => $data['legal_basis'],
                        'retention_policy_version' => $data['retention_policy_version'],
                        'request_count' => DB::raw('request_count + 1'),
                        'last_requested_at' => now(),
                        'updated_at' => now(),
                    ]);

                $coupon = (array) DB::table('promotion_coupons')->where('id', $existing->id)->first();
                $this->recordAuthorization($coupon, $data);
                $this->ensureOutbox($coupon, $data);

                return $coupon;
            }

            $publicRequestId = (string) Str::uuid();
            $inserted = DB::table('promotion_coupons')->insertOrIgnore([
                'campaign_id' => $campaignId,
                'purpose' => $data['purpose'],
                'public_request_id' => $publicRequestId,
                'email_canonical' => $data['email_canonical'],
                'email_for_delivery' => $data['email_for_delivery'],
                'email_fingerprint' => $data['email_fingerprint'],
                'email_fingerprint_key_version' => $data['email_fingerprint_key_version'],
                'authorization_text_version' => $data['authorization_text_version'],
                'authorization_accepted_at' => $data['authorization_accepted_at'],
                'legal_basis' => $data['legal_basis'],
                'retention_policy_version' => $data['retention_policy_version'],
                'revoked_at' => null,
                'revoked_reason' => null,
                'suppressed_at' => null,
                'suppressed_reason' => null,
                'anonymized_at' => null,
                'anonymized_reason' => null,
                'code_digest' => $data['code_digest'],
                'code_encrypted' => $data['code_encrypted'],
                'code_key_version' => $data['code_key_version'],
                'discount_percent' => $data['discount_percent'],
                'minimum_amount' => $data['minimum_amount'],
                'non_cumulative' => $data['non_cumulative'],
                'manual_checkout_required' => $data['manual_checkout_required'],
                'expires_at' => null,
                'delivery_mode' => $data['delivery_mode'],
                'issuance_state' => 'issued',
                'delivery_state' => $data['delivery_mode'] === 'email' ? 'pending' : 'displayed',
                'user_agent_hash' => $data['user_agent_hash'],
                'request_count' => 1,
                'last_requested_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($inserted === 1) {
                $coupon = (array) DB::table('promotion_coupons')
                    ->where('public_request_id', $publicRequestId)
                    ->first();
            } else {
                $concurrent = $this->findActiveCoupon($data['email_fingerprints'], true);

                if (! $concurrent) {
                    throw new RuntimeException('Não foi possível persistir um código promocional único.');
                }

                $coupon = (array) $concurrent;
            }

            $this->recordAuthorization($coupon, $data);
            $this->ensureOutbox($coupon, $data);

            return $coupon;
        }, 3);
    }

    public function anonymizeByFingerprints(array $fingerprints, string $reason): int
    {
        return DB::transaction(function () use ($fingerprints, $reason): int {
            $query = DB::table('promotion_coupons')
                ->where('purpose', 'first_purchase')
                ->whereNull('anonymized_at');
            $this->whereAnyFingerprint($query, $fingerprints);
            $ids = $query->lockForUpdate()->pluck('id');

            if ($ids->isEmpty()) {
                return 0;
            }

            return $this->anonymizeCouponIds($ids->all(), $reason);
        });
    }

    public function exportByFingerprints(array $fingerprints): array
    {
        $query = DB::table('promotion_coupons')->where('purpose', 'first_purchase');
        $this->whereAnyFingerprint($query, $fingerprints);

        return $query
            ->get([
                'public_request_id',
                'email_for_delivery',
                'authorization_text_version',
                'authorization_accepted_at',
                'legal_basis',
                'retention_policy_version',
                'discount_percent',
                'minimum_amount',
                'non_cumulative',
                'manual_checkout_required',
                'delivery_mode',
                'issuance_state',
                'delivery_state',
                'revoked_at',
                'suppressed_at',
                'created_at',
            ])
            ->map(static fn (object $row): array => (array) $row)
            ->all();
    }

    public function revokeByFingerprints(array $fingerprints, string $reason): int
    {
        return $this->stopFutureDelivery($fingerprints, 'revoked', $reason);
    }

    public function suppressByFingerprints(array $fingerprints, string $reason): int
    {
        return $this->stopFutureDelivery($fingerprints, 'suppressed', $reason);
    }

    public function deleteByFingerprints(array $fingerprints): int
    {
        $query = DB::table('promotion_coupons')->where('purpose', 'first_purchase');
        $this->whereAnyFingerprint($query, $fingerprints);

        return $query->delete();
    }

    public function anonymizeRetainedBefore(\DateTimeInterface $cutoff, string $reason): int
    {
        return DB::transaction(function () use ($cutoff, $reason): int {
            $ids = DB::table('promotion_coupons')
                ->where('purpose', 'first_purchase')
                ->whereNull('anonymized_at')
                ->where('created_at', '<', $cutoff)
                ->lockForUpdate()
                ->pluck('id')
                ->all();

            return $ids === [] ? 0 : $this->anonymizeCouponIds($ids, $reason);
        });
    }

    private function findActiveCoupon(array $fingerprints, bool $lock): ?object
    {
        $query = DB::table('promotion_coupons')
            ->where('purpose', 'first_purchase')
            ->whereNull('revoked_at')
            ->whereNull('suppressed_at')
            ->whereNull('anonymized_at');
        $this->whereAnyFingerprint($query, $fingerprints);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function whereAnyFingerprint(Builder $query, array $fingerprints): void
    {
        $query->where(function (Builder $fingerprintQuery) use ($fingerprints): void {
            foreach ($fingerprints as $version => $fingerprint) {
                $fingerprintQuery->orWhere(function (Builder $versionQuery) use ($version, $fingerprint): void {
                    $versionQuery
                        ->where('email_fingerprint_key_version', $version)
                        ->where('email_fingerprint', $fingerprint);
                });
            }
        });
    }

    private function stopFutureDelivery(array $fingerprints, string $state, string $reason): int
    {
        return DB::transaction(function () use ($fingerprints, $state, $reason): int {
            $query = DB::table('promotion_coupons')->where('purpose', 'first_purchase');
            $this->whereAnyFingerprint($query, $fingerprints);
            $ids = $query->lockForUpdate()->pluck('id');

            if ($ids->isEmpty()) {
                return 0;
            }

            DB::table('promotion_outbox')
                ->whereIn('coupon_id', $ids)
                ->whereIn('state', ['pending', 'queued', 'processing', 'failed_retryable'])
                ->update([
                    'state' => 'failed_final',
                    'failed_at' => now(),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);

            if ($state === 'revoked') {
                DB::table('promotion_authorizations')
                    ->whereIn('coupon_id', $ids)
                    ->whereNull('revoked_at')
                    ->update([
                        'revoked_at' => now(),
                        'revoked_reason' => $reason,
                        'updated_at' => now(),
                    ]);
            }

            $timestampColumn = $state.'_at';
            $reasonColumn = $state.'_reason';

            return DB::table('promotion_coupons')->whereIn('id', $ids)->update([
                $timestampColumn => now(),
                $reasonColumn => $reason,
                'delivery_state' => $state,
                'updated_at' => now(),
            ]);
        });
    }

    /** @param list<int> $ids */
    private function anonymizeCouponIds(array $ids, string $reason): int
    {
        DB::table('promotion_outbox')
            ->whereIn('coupon_id', $ids)
            ->whereIn('state', ['pending', 'queued', 'processing', 'failed_retryable'])
            ->delete();

        DB::table('promotion_outbox')
            ->whereIn('coupon_id', $ids)
            ->update(['last_error' => null, 'updated_at' => now()]);

        return DB::table('promotion_coupons')
            ->whereIn('id', $ids)
            ->update([
                'email_canonical' => null,
                'email_for_delivery' => null,
                'email_fingerprint' => null,
                'email_fingerprint_key_version' => null,
                'user_agent_hash' => null,
                'code_digest' => null,
                'code_encrypted' => null,
                'code_key_version' => null,
                'delivery_state' => 'anonymized',
                'anonymized_at' => now(),
                'anonymized_reason' => $reason,
                'updated_at' => now(),
            ]);
    }

    private function activeCampaignId(array $data): int
    {
        $termsHash = hash('sha256', (string) json_encode([
            $data['discount_percent'],
            $data['minimum_amount'],
            $data['non_cumulative'],
            $data['manual_checkout_required'],
            $data['authorization_text_version'],
            $data['legal_basis'],
            $data['retention_policy_version'],
            $data['delivery_mode'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $campaign = DB::table('promotion_campaigns')
            ->where('purpose', 'first_purchase')
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();

        if ($campaign && hash_equals((string) $campaign->terms_hash, $termsHash)) {
            return (int) $campaign->id;
        }

        if ($campaign) {
            DB::table('promotion_campaigns')->where('id', $campaign->id)->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);
        }

        $slug = 'first-purchase-'.substr($termsHash, 0, 16);
        DB::table('promotion_campaigns')->insertOrIgnore([
            'purpose' => 'first_purchase',
            'slug' => $slug,
            'name' => 'Cupom de primeira compra',
            'terms_hash' => $termsHash,
            'is_active' => true,
            'discount_percent' => $data['discount_percent'],
            'minimum_amount' => $data['minimum_amount'],
            'non_cumulative' => $data['non_cumulative'],
            'manual_checkout_required' => $data['manual_checkout_required'],
            'authorization_text_version' => $data['authorization_text_version'],
            'legal_basis' => $data['legal_basis'],
            'retention_policy_version' => $data['retention_policy_version'],
            'delivery_mode' => $data['delivery_mode'],
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $created = DB::table('promotion_campaigns')
            ->where('purpose', 'first_purchase')
            ->where('terms_hash', $termsHash)
            ->first();

        if (! $created) {
            throw new RuntimeException('Não foi possível ativar a campanha de primeira compra.');
        }

        if (! $created->is_active) {
            DB::table('promotion_campaigns')->where('id', $created->id)->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);
        }

        return (int) $created->id;
    }

    private function recordAuthorization(array $coupon, array $data): void
    {
        DB::table('promotion_authorizations')->insertOrIgnore([
            'coupon_id' => $coupon['id'],
            'text_version' => $data['authorization_text_version'],
            'legal_basis' => $data['legal_basis'],
            'retention_policy_version' => $data['retention_policy_version'],
            'accepted_at' => $data['authorization_accepted_at'],
            'revoked_at' => null,
            'revoked_reason' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureOutbox(array $coupon, array $data): void
    {
        if ($data['delivery_mode'] !== 'email') {
            return;
        }

        DB::table('promotion_outbox')->insertOrIgnore([
            'coupon_id' => $coupon['id'],
            'notification_key' => implode(':', [
                'coupon',
                (string) $coupon['id'],
                'email',
                'first_purchase_coupon',
                $data['mail_template_version'],
            ]),
            'channel' => 'email',
            'purpose' => 'first_purchase_coupon',
            'template_version' => $data['mail_template_version'],
            'state' => 'pending',
            'available_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
