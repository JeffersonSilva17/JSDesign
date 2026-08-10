<?php

namespace App\Modules\Promotions\Application;

use App\Modules\Promotions\Domain\CouponCodeGenerator;
use App\Modules\Promotions\Domain\EmailCanonicalizer;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use App\Modules\Promotions\Domain\PromotionCodeCipher;
use App\Modules\Promotions\Domain\PromotionCouponRepository;
use App\Modules\Promotions\Infrastructure\Delivery\EmailProvider;
use Illuminate\Support\Str;

final readonly class IssueOrAssociateFirstPurchaseCoupon
{
    public function __construct(
        private FirstPurchasePromotionSettings $settings,
        private EmailCanonicalizer $emailCanonicalizer,
        private CouponCodeGenerator $couponCodeGenerator,
        private PromotionCodeCipher $codeCipher,
        private PromotionCouponRepository $repository,
        private EmailProvider $emailProvider,
    ) {}

    public function handle(string $email, string $authorizationTextVersion, ?string $userAgent): IssueFirstPurchaseCouponResult
    {
        $this->settings->assertRequestAllowed();

        $emailData = $this->emailCanonicalizer->canonicalize($email);
        $fingerprintKeyVersion = $this->settings->activeHmacKeyVersion();
        $fingerprints = $this->settings->emailFingerprints($emailData['canonical']);
        $fingerprint = $fingerprints[$fingerprintKeyVersion];
        $code = $this->couponCodeGenerator->generate();
        $encryptedCode = $this->codeCipher->encrypt($code);

        $coupon = $this->repository->issueOrFind([
            'purpose' => 'first_purchase',
            'email_canonical' => $emailData['canonical'],
            'email_for_delivery' => $emailData['delivery'],
            'email_fingerprint' => $fingerprint,
            'email_fingerprint_key_version' => $fingerprintKeyVersion,
            'email_fingerprints' => $fingerprints,
            'authorization_text_version' => $authorizationTextVersion,
            'authorization_accepted_at' => now(),
            'legal_basis' => $this->settings->legalBasis(),
            'retention_policy_version' => $this->settings->retentionPolicyVersion(),
            'discount_percent' => $this->settings->discountPercent(),
            'minimum_amount' => $this->settings->minimumAmount(),
            'non_cumulative' => $this->settings->nonCumulative(),
            'manual_checkout_required' => $this->settings->manualCheckoutRequired(),
            'delivery_mode' => $this->settings->deliveryMode(),
            'code_plain' => $code,
            'code_digest' => hash_hmac('sha256', $code, $this->settings->hmacKey($fingerprintKeyVersion)),
            'code_encrypted' => $encryptedCode['encrypted'],
            'code_key_version' => $encryptedCode['key_version'],
            'mail_template_version' => $this->settings->mailTemplateVersion(),
            'user_agent_hash' => $userAgent ? hash('sha256', Str::limit($userAgent, 512, '')) : null,
        ]);

        if ($this->settings->deliveryMode() === 'email') {
            $this->emailProvider->queueFirstPurchaseCoupon((int) $coupon['id']);
        }

        $displayCode = $this->settings->deliveryMode() === 'display'
            ? $this->codeCipher->decrypt((string) $coupon['code_encrypted'], (string) $coupon['code_key_version'])
            : null;

        return new IssueFirstPurchaseCouponResult(
            delivery: $this->settings->deliveryMode(),
            message: $this->settings->deliveryMode() === 'display'
                ? 'Cupom emitido para teste. Copie o código e use manualmente no checkout quando estiver disponível.'
                : 'Solicitação aceita. Se elegível, o cupom será entregue pelo e-mail informado.',
            requestId: (string) $coupon['public_request_id'],
            couponCode: $displayCode,
        );
    }
}
