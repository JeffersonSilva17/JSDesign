<?php

namespace Tests\Feature;

use App\Modules\Promotions\Application\IssueOrAssociateFirstPurchaseCoupon;
use App\Modules\Promotions\Application\Privacy\AnonymizeFirstPurchaseCoupon;
use App\Modules\Promotions\Application\Privacy\ManageFirstPurchaseCouponPrivacy;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use App\Modules\Promotions\Domain\PromotionCodeCipher;
use App\Modules\Promotions\Infrastructure\Delivery\Jobs\DeliverFirstPurchaseCoupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class FirstPurchaseCouponTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_offer_returns_disabled_when_real_data_gate_is_incomplete(): void
    {
        Config::set('promotions.first_purchase.enabled', true);
        Config::set('promotions.first_purchase.real_data_allowed', true);
        Config::set('promotions.first_purchase.delivery_mode', 'email');
        Config::set('promotions.first_purchase.provider_ready', false);

        $this->getJson('/api/v1/promotions/first-purchase-offer')
            ->assertOk()
            ->assertJsonPath('enabled', false)
            ->assertJsonMissingPath('internal_url');
    }

    public function test_offer_returns_sanitized_display_contract_for_testing(): void
    {
        $this->enableTestingDisplayPromotion();

        $this->getJson('/api/v1/promotions/first-purchase-offer')
            ->assertOk()
            ->assertExactJson([
                'enabled' => true,
                'delivery_mode' => 'display',
                'discount_percent' => 10,
                'minimum_amount' => null,
                'non_cumulative' => true,
                'manual_checkout_required' => true,
                'authorization_text_version' => 'coupon-v1',
                'offer_id' => $this->offerId(),
                'texts' => [
                    'title' => 'Ganhe 10% na primeira compra',
                    'description' => 'Solicite um cupom individual por e-mail. O desconto será validado no checkout quando essa etapa estiver disponível.',
                    'authorization' => 'Autorizo o uso deste e-mail somente para emitir e entregar meu cupom de primeira compra.',
                    'privacy_url' => '/privacidade',
                ],
            ]);
    }

    public function test_request_requires_valid_email_and_authorization(): void
    {
        $this->enableTestingDisplayPromotion();

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'invalido',
            'authorization_accepted' => false,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'authorization_accepted']);
    }

    public function test_request_rejects_browser_supplied_stale_authorization_version(): void
    {
        $this->enableTestingDisplayPromotion();

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'cliente@example.com',
            'authorization_accepted' => true,
            'authorization_text_version' => 'old-version',
            'offer_id' => $this->offerId(),
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['authorization_text_version']);
    }

    public function test_display_mode_is_idempotent_and_does_not_store_plain_email_in_logs(): void
    {
        $this->enableTestingDisplayPromotion();
        Log::spy();

        $payload = [
            'email' => ' Cliente@Exemplo.COM ',
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ];

        $first = $this->postJson('/api/v1/promotions/first-purchase-coupons', $payload)
            ->assertOk()
            ->assertJsonPath('status', 'accepted')
            ->assertJsonPath('delivery', 'display')
            ->assertJsonStructure(['status', 'delivery', 'message', 'request_id', 'coupon_code']);

        $second = $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            ...$payload,
            'email' => 'cliente@exemplo.com',
        ])->assertOk();

        self::assertSame($first->json('coupon_code'), $second->json('coupon_code'));
        self::assertSame(1, DB::table('promotion_coupons')->count());
        Log::shouldNotHaveReceived('error');
    }

    public function test_email_mode_never_returns_coupon_code_and_creates_one_outbox_item(): void
    {
        $this->enableEmailPromotion();

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'cliente@example.com',
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ])->assertAccepted()
            ->assertJsonPath('delivery', 'email')
            ->assertJsonMissingPath('coupon_code');

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'cliente@example.com',
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ])->assertAccepted()
            ->assertJsonMissingPath('coupon_code');

        self::assertSame(1, DB::table('promotion_coupons')->count());
        self::assertSame(1, DB::table('promotion_outbox')->count());
        Queue::assertPushed(DeliverFirstPurchaseCoupon::class, 1);
    }

    public function test_production_refuses_display_delivery_mode(): void
    {
        Config::set('app.env', 'production');
        Config::set('promotions.first_purchase.enabled', true);
        Config::set('promotions.first_purchase.real_data_allowed', true);
        Config::set('promotions.first_purchase.delivery_mode', 'display');
        Config::set('promotions.first_purchase.test_display_allowed', false);

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'cliente@example.com',
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ])->assertStatus(503)
            ->assertJsonMissingPath('coupon_code');
    }

    public function test_retention_anonymizes_matching_coupon_without_public_listing(): void
    {
        $this->enableTestingDisplayPromotion();

        $this->postJson('/api/v1/promotions/first-purchase-coupons', [
            'email' => 'cliente@example.com',
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ])->assertOk();

        $service = app(AnonymizeFirstPurchaseCoupon::class);
        $count = $service->anonymizeByEmail('cliente@example.com', 'solicitação da titular');

        self::assertSame(1, $count);
        $record = DB::table('promotion_coupons')->first();

        self::assertNotNull($record->anonymized_at);
        self::assertNull($record->email_canonical);
        self::assertNull($record->email_for_delivery);
        self::assertNull($record->email_fingerprint);
        self::assertNull($record->code_encrypted);
    }

    public function test_hmac_rotation_reuses_and_anonymizes_coupon_from_old_key(): void
    {
        $this->enableTestingDisplayPromotion();
        $payload = $this->validPayload('Cliente@example.com');

        $firstCode = $this->postJson('/api/v1/promotions/first-purchase-coupons', $payload)
            ->assertOk()
            ->json('coupon_code');

        Config::set('promotions.first_purchase.hmac_keys', [
            'v1' => 'test-hmac-key',
            'v2' => 'rotated-test-hmac-key',
        ]);
        Config::set('promotions.first_purchase.active_hmac_key_version', 'v2');

        $this->postJson('/api/v1/promotions/first-purchase-coupons', $this->validPayload('cliente@example.com'))
            ->assertOk()
            ->assertJsonPath('coupon_code', $firstCode);

        self::assertSame(1, DB::table('promotion_coupons')->count());
        self::assertSame(1, app(AnonymizeFirstPurchaseCoupon::class)->anonymizeByEmail('cliente@example.com', 'direito da titular'));
    }

    public function test_encryption_rotation_reads_existing_coupon_by_recorded_version(): void
    {
        $this->enableTestingDisplayPromotion();
        $firstCode = $this->postJson('/api/v1/promotions/first-purchase-coupons', $this->validPayload())
            ->assertOk()
            ->json('coupon_code');

        Config::set('promotions.first_purchase.encryption_keys', [
            'v1' => base64_encode(str_repeat('a', 32)),
            'v2' => base64_encode(str_repeat('b', 32)),
        ]);
        Config::set('promotions.first_purchase.active_encryption_key_version', 'v2');

        $this->postJson('/api/v1/promotions/first-purchase-coupons', $this->validPayload())
            ->assertOk()
            ->assertJsonPath('coupon_code', $firstCode);
    }

    public function test_sent_outbox_is_not_rearmed_by_repeat_request(): void
    {
        $this->enableEmailPromotion();
        $payload = $this->validPayload();

        $this->postJson('/api/v1/promotions/first-purchase-coupons', $payload)->assertAccepted();
        DB::table('promotion_outbox')->update(['state' => 'sent', 'sent_at' => now()]);
        DB::table('promotion_coupons')->update(['delivery_state' => 'sent']);

        $this->postJson('/api/v1/promotions/first-purchase-coupons', $payload)->assertAccepted();

        self::assertSame('sent', DB::table('promotion_outbox')->value('state'));
        Queue::assertPushed(DeliverFirstPurchaseCoupon::class, 1);
    }

    public function test_concurrent_first_requests_return_the_same_persisted_coupon(): void
    {
        Config::set('database.connections.promotion_cleanup', config('database.connections.pgsql'));
        $cleanup = DB::connection('promotion_cleanup');
        $cleanup->table('promotion_campaigns')->where('purpose', 'first_purchase')->delete();

        $task = static function (): array {
            Config::set('app.env', 'testing');
            Config::set('promotions.first_purchase.enabled', true);
            Config::set('promotions.first_purchase.real_data_allowed', false);
            Config::set('promotions.first_purchase.delivery_mode', 'display');
            Config::set('promotions.first_purchase.test_display_allowed', true);
            Config::set('promotions.first_purchase.authorization_text_version', 'coupon-v1');
            Config::set('promotions.first_purchase.legal_basis', 'consent');
            Config::set('promotions.first_purchase.retention_policy_version', 'coupon-retention-v1');
            Config::set('promotions.first_purchase.hmac_keys', ['v1' => 'concurrency-hmac-key']);
            Config::set('promotions.first_purchase.active_hmac_key_version', 'v1');
            Config::set('promotions.first_purchase.encryption_keys', [
                'v1' => base64_encode(str_repeat('c', 32)),
            ]);
            Config::set('promotions.first_purchase.active_encryption_key_version', 'v1');

            $result = app(IssueOrAssociateFirstPurchaseCoupon::class)->handle(
                'concorrencia@example.com',
                'coupon-v1',
                null,
            );

            return [$result->requestId, $result->couponCode];
        };

        try {
            $results = Concurrency::driver('process')->run([$task, $task], timeout: 15);

            self::assertSame($results[0], $results[1]);
            self::assertSame(
                1,
                $cleanup->table('promotion_coupons')->where('email_for_delivery', 'concorrencia@example.com')->count(),
            );
        } finally {
            $cleanup->table('promotion_campaigns')->where('purpose', 'first_purchase')->delete();
            DB::purge('promotion_cleanup');
        }
    }

    public function test_delivery_job_sends_once_and_terminal_state_blocks_duplicate_worker(): void
    {
        $this->enableEmailPromotion();
        $this->postJson('/api/v1/promotions/first-purchase-coupons', $this->validPayload())->assertAccepted();

        $outboxId = (int) DB::table('promotion_outbox')->value('id');
        $job = new DeliverFirstPurchaseCoupon($outboxId);
        $job->handle(app(PromotionCodeCipher::class));
        $job->handle(app(PromotionCodeCipher::class));

        self::assertSame('sent', DB::table('promotion_outbox')->value('state'));
        self::assertSame(1, (int) DB::table('promotion_outbox')->value('attempts'));
        self::assertSame('sent', DB::table('promotion_coupons')->value('delivery_state'));
    }

    public function test_privacy_commands_export_revoke_suppress_and_delete_without_future_delivery(): void
    {
        $this->enableEmailPromotion();
        $this->postJson('/api/v1/promotions/first-purchase-coupons', $this->validPayload())->assertAccepted();
        $privacy = app(ManageFirstPurchaseCouponPrivacy::class);

        self::assertCount(1, $privacy->exportByEmail('cliente@example.com'));
        self::assertSame(1, $privacy->revokeByEmail('cliente@example.com', 'consentimento revogado'));
        self::assertNotNull(DB::table('promotion_authorizations')->value('revoked_at'));
        self::assertSame('failed_final', DB::table('promotion_outbox')->value('state'));
        self::assertSame(1, $privacy->suppressByEmail('cliente@example.com', 'bloqueio solicitado'));
        self::assertSame(1, $privacy->deleteByEmail('cliente@example.com'));
        self::assertSame(0, DB::table('promotion_coupons')->count());
        self::assertSame(0, DB::table('promotion_outbox')->count());
    }

    private function enableTestingDisplayPromotion(): void
    {
        Config::set('app.env', 'testing');
        Config::set('promotions.first_purchase.enabled', true);
        Config::set('promotions.first_purchase.real_data_allowed', false);
        Config::set('promotions.first_purchase.delivery_mode', 'display');
        Config::set('promotions.first_purchase.test_display_allowed', true);
        Config::set('promotions.first_purchase.provider_ready', false);
        Config::set('promotions.first_purchase.trusted_proxies', ['127.0.0.1']);
        Config::set('promotions.first_purchase.authorization_text_version', 'coupon-v1');
        Config::set('promotions.first_purchase.legal_basis', 'consent');
        Config::set('promotions.first_purchase.retention_policy_version', 'coupon-retention-v1');
        Config::set('promotions.first_purchase.hmac_keys', ['v1' => 'test-hmac-key']);
        Config::set('promotions.first_purchase.active_hmac_key_version', 'v1');
        Config::set('promotions.first_purchase.encryption_keys', ['v1' => base64_encode(str_repeat('a', 32))]);
        Config::set('promotions.first_purchase.active_encryption_key_version', 'v1');
    }

    private function enableEmailPromotion(): void
    {
        $this->enableTestingDisplayPromotion();
        Queue::fake();
        Config::set('mail.default', 'array');
        Config::set('promotions.first_purchase.real_data_allowed', true);
        Config::set('promotions.first_purchase.delivery_mode', 'email');
        Config::set('promotions.first_purchase.provider_ready', true);
        Config::set('promotions.first_purchase.worker_required', false);
        Config::set('promotions.first_purchase.worker_ready', true);
        Config::set('promotions.first_purchase.published_privacy_policy', true);
    }

    /** @return array<string, mixed> */
    private function validPayload(string $email = 'cliente@example.com'): array
    {
        return [
            'email' => $email,
            'authorization_accepted' => true,
            'authorization_text_version' => 'coupon-v1',
            'offer_id' => $this->offerId(),
        ];
    }

    private function offerId(): string
    {
        return app(FirstPurchasePromotionSettings::class)->offerId();
    }
}
