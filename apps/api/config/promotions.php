<?php

return [
    'first_purchase' => [
        'enabled' => env('PROMO_FIRST_PURCHASE_ENABLED', false),
        'delivery_mode' => env('PROMO_FIRST_PURCHASE_DELIVERY', 'email'),
        'discount_percent' => (int) env('PROMO_FIRST_PURCHASE_PERCENT', 10),
        'minimum_amount' => blank(env('PROMO_FIRST_PURCHASE_MINIMUM_AMOUNT'))
            ? null
            : (float) env('PROMO_FIRST_PURCHASE_MINIMUM_AMOUNT'),
        'non_cumulative' => env('PROMO_FIRST_PURCHASE_NON_CUMULATIVE', true),
        'manual_checkout_required' => env('PROMO_FIRST_PURCHASE_MANUAL_CHECKOUT_REQUIRED', true),
        'authorization_text_version' => env('PROMO_FIRST_PURCHASE_AUTHORIZATION_TEXT_VERSION', 'coupon-v1'),
        'legal_basis' => env('PROMO_FIRST_PURCHASE_LEGAL_BASIS'),
        'retention_policy_version' => env('PROMO_FIRST_PURCHASE_RETENTION_POLICY_VERSION'),
        'real_data_allowed' => env('PROMO_FIRST_PURCHASE_REAL_DATA_ALLOWED', false),
        'published_privacy_policy' => env('PROMO_FIRST_PURCHASE_PRIVACY_PUBLISHED', false),
        'provider_ready' => env('PROMO_FIRST_PURCHASE_PROVIDER_READY', false),
        'worker_required' => env('PROMO_FIRST_PURCHASE_WORKER_REQUIRED', true),
        'worker_ready' => env('PROMO_FIRST_PURCHASE_WORKER_READY', false),
        'test_display_allowed' => env('PROMO_FIRST_PURCHASE_TEST_DISPLAY_ALLOWED', false),
        'test_environment' => env('PROMO_FIRST_PURCHASE_TEST_ENVIRONMENT', false),
        'trusted_proxies' => array_values(array_filter(array_map(
            static fn (string $proxy): string => trim($proxy),
            explode(',', (string) env('TRUSTED_PROXIES', '')),
        ))),
        'ip_limit_per_minute' => (int) env('PROMO_FIRST_PURCHASE_IP_LIMIT_PER_MINUTE', 5),
        'email_limit_per_hour' => (int) env('PROMO_FIRST_PURCHASE_EMAIL_LIMIT_PER_HOUR', 3),
        'active_hmac_key_version' => env('PROMO_FIRST_PURCHASE_HMAC_KEY_VERSION', 'v1'),
        'hmac_keys' => [
            'v1' => env('PROMO_FIRST_PURCHASE_HMAC_KEY', env('APP_KEY', 'local-development-only')),
        ],
        'active_encryption_key_version' => env('PROMO_FIRST_PURCHASE_ENCRYPTION_KEY_VERSION', 'v1'),
        'encryption_keys' => [
            'v1' => env('PROMO_FIRST_PURCHASE_ENCRYPTION_KEY', env('APP_KEY', 'local-development-only')),
        ],
        'mail_template_version' => env('PROMO_FIRST_PURCHASE_MAIL_TEMPLATE_VERSION', 'first-purchase-v1'),
        'texts' => [
            'title' => env('PROMO_FIRST_PURCHASE_TITLE', 'Ganhe 10% na primeira compra'),
            'description' => env('PROMO_FIRST_PURCHASE_DESCRIPTION', 'Solicite um cupom individual por e-mail. O desconto será validado no checkout quando essa etapa estiver disponível.'),
            'authorization' => env('PROMO_FIRST_PURCHASE_AUTHORIZATION', 'Autorizo o uso deste e-mail somente para emitir e entregar meu cupom de primeira compra.'),
            'privacy_url' => env('PROMO_FIRST_PURCHASE_PRIVACY_URL', '/privacidade'),
        ],
    ],
];
