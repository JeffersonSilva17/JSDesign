<?php

namespace App\Modules\Promotions\Domain;

final class FirstPurchasePromotionSettings
{
    /**
     * @return array<string, mixed>
     */
    public function publicOffer(): array
    {
        $enabled = $this->isPubliclyEnabled();

        $payload = [
            'enabled' => $enabled,
            'delivery_mode' => $this->deliveryMode(),
            'discount_percent' => $this->discountPercent(),
            'minimum_amount' => $this->minimumAmount(),
            'non_cumulative' => $this->nonCumulative(),
            'manual_checkout_required' => $this->manualCheckoutRequired(),
            'authorization_text_version' => $this->authorizationTextVersion(),
            'offer_id' => $this->offerId(),
        ];

        if ($enabled) {
            $payload['texts'] = config('promotions.first_purchase.texts');
        }

        return $payload;
    }

    public function assertRequestAllowed(): void
    {
        if (! $this->isPubliclyEnabled()) {
            throw new PromotionUnavailable('Promoção indisponível.');
        }

        if ($this->deliveryMode() === 'display' && ! $this->displayAllowed()) {
            throw new PromotionUnavailable('Modo display indisponível.');
        }
    }

    public function isPubliclyEnabled(): bool
    {
        if (! (bool) config('promotions.first_purchase.enabled') || ! $this->commonConfigurationReady()) {
            return false;
        }

        if ($this->deliveryMode() === 'display') {
            return $this->displayAllowed();
        }

        return $this->realDataGateReady();
    }

    public function realDataGateReady(): bool
    {
        $texts = config('promotions.first_purchase.texts', []);
        $mailer = (string) config('mail.default');

        return $this->deliveryMode() === 'email'
            && (bool) config('promotions.first_purchase.real_data_allowed')
            && (bool) config('promotions.first_purchase.published_privacy_policy')
            && filled(config('promotions.first_purchase.legal_basis'))
            && filled(config('promotions.first_purchase.retention_policy_version'))
            && filled($this->authorizationTextVersion())
            && filled($texts['title'] ?? null)
            && filled($texts['description'] ?? null)
            && filled($texts['authorization'] ?? null)
            && $this->validPrivacyUrl($texts['privacy_url'] ?? null)
            && (bool) config('promotions.first_purchase.provider_ready')
            && (! (bool) config('promotions.first_purchase.worker_required')
                || (bool) config('promotions.first_purchase.worker_ready'))
            && (app()->environment('testing') || ! in_array($mailer, ['array', 'log'], true))
            && count($this->trustedProxies()) > 0
            && $this->discountPercent() > 0
            && $this->discountPercent() <= 100
            && $this->minimumAmount() === null
            && count($this->hmacKeys()) > 0
            && filled($this->hmacKey())
            && filled($this->encryptionKey());
    }

    public function displayAllowed(): bool
    {
        return (in_array(app()->environment(), ['local', 'testing'], true)
                || (bool) config('promotions.first_purchase.test_environment'))
            && ! (bool) config('promotions.first_purchase.real_data_allowed')
            && (bool) config('promotions.first_purchase.test_display_allowed');
    }

    public function offerId(): string
    {
        $terms = [
            'purpose' => 'first_purchase',
            'authorization_text_version' => $this->authorizationTextVersion(),
            'delivery_mode' => $this->deliveryMode(),
            'discount_percent' => $this->discountPercent(),
            'minimum_amount' => $this->minimumAmount(),
            'non_cumulative' => $this->nonCumulative(),
            'manual_checkout_required' => $this->manualCheckoutRequired(),
            'texts' => config('promotions.first_purchase.texts'),
        ];

        return 'first_purchase:'.hash('sha256', (string) json_encode($terms, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function deliveryMode(): string
    {
        return (string) config('promotions.first_purchase.delivery_mode', 'email');
    }

    public function hasValidDeliveryMode(): bool
    {
        return in_array($this->deliveryMode(), ['display', 'email'], true);
    }

    public function discountPercent(): int
    {
        return (int) config('promotions.first_purchase.discount_percent', 10);
    }

    public function minimumAmount(): ?float
    {
        $value = config('promotions.first_purchase.minimum_amount');

        return $value === null ? null : (float) $value;
    }

    public function nonCumulative(): bool
    {
        return (bool) config('promotions.first_purchase.non_cumulative', true);
    }

    public function manualCheckoutRequired(): bool
    {
        return (bool) config('promotions.first_purchase.manual_checkout_required', true);
    }

    public function authorizationTextVersion(): string
    {
        return (string) config('promotions.first_purchase.authorization_text_version');
    }

    public function legalBasis(): ?string
    {
        $value = config('promotions.first_purchase.legal_basis');

        return $value === null ? null : (string) $value;
    }

    public function retentionPolicyVersion(): ?string
    {
        $value = config('promotions.first_purchase.retention_policy_version');

        return $value === null ? null : (string) $value;
    }

    public function activeHmacKeyVersion(): string
    {
        return (string) config('promotions.first_purchase.active_hmac_key_version', 'v1');
    }

    public function hmacKey(?string $version = null): string
    {
        $keyVersion = $version ?? $this->activeHmacKeyVersion();

        return (string) config("promotions.first_purchase.hmac_keys.$keyVersion", '');
    }

    /**
     * @return array<string, string>
     */
    public function hmacKeys(): array
    {
        return array_filter(
            (array) config('promotions.first_purchase.hmac_keys', []),
            static fn (mixed $key, mixed $version): bool => is_string($version) && is_string($key) && filled($key),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * @return array<string, string>
     */
    public function emailFingerprints(string $canonicalEmail): array
    {
        $fingerprints = [];

        foreach ($this->hmacKeys() as $version => $key) {
            $fingerprints[$version] = hash_hmac('sha256', $canonicalEmail, $key);
        }

        return $fingerprints;
    }

    public function activeEncryptionKeyVersion(): string
    {
        return (string) config('promotions.first_purchase.active_encryption_key_version', 'v1');
    }

    public function encryptionKey(?string $version = null): string
    {
        $keyVersion = $version ?? $this->activeEncryptionKeyVersion();

        return (string) config("promotions.first_purchase.encryption_keys.$keyVersion", '');
    }

    /**
     * @return list<string>
     */
    public function trustedProxies(): array
    {
        return array_values((array) config('promotions.first_purchase.trusted_proxies', []));
    }

    public function ipLimitPerMinute(): int
    {
        return max(1, (int) config('promotions.first_purchase.ip_limit_per_minute', 5));
    }

    public function emailLimitPerHour(): int
    {
        return max(1, (int) config('promotions.first_purchase.email_limit_per_hour', 3));
    }

    public function mailTemplateVersion(): string
    {
        return (string) config('promotions.first_purchase.mail_template_version', 'first-purchase-v1');
    }

    private function validPrivacyUrl(mixed $url): bool
    {
        return is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//');
    }

    private function commonConfigurationReady(): bool
    {
        $texts = config('promotions.first_purchase.texts', []);

        return $this->hasValidDeliveryMode()
            && $this->discountPercent() > 0
            && $this->discountPercent() <= 100
            && $this->minimumAmount() === null
            && filled($this->authorizationTextVersion())
            && filled($texts['title'] ?? null)
            && filled($texts['description'] ?? null)
            && filled($texts['authorization'] ?? null)
            && $this->validPrivacyUrl($texts['privacy_url'] ?? null)
            && count($this->hmacKeys()) > 0
            && filled($this->hmacKey())
            && filled($this->encryptionKey());
    }
}
