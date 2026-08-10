<?php

namespace App\Modules\Promotions\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Promotions\Application\IssueOrAssociateFirstPurchaseCoupon;
use App\Modules\Promotions\Domain\EmailCanonicalizer;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use App\Modules\Promotions\Domain\PromotionUnavailable;
use App\Modules\Promotions\Interfaces\Http\Requests\FirstPurchaseCouponRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

final class FirstPurchaseCouponController extends Controller
{
    public function __invoke(
        FirstPurchaseCouponRequest $request,
        FirstPurchasePromotionSettings $settings,
        EmailCanonicalizer $emailCanonicalizer,
        IssueOrAssociateFirstPurchaseCoupon $issuer,
    ): JsonResponse {
        try {
            $settings->assertRequestAllowed();
        } catch (PromotionUnavailable) {
            return $this->unavailable();
        }

        $ipKey = 'promotion:first_purchase:ip:'.$request->ip();

        if (RateLimiter::tooManyAttempts($ipKey, $settings->ipLimitPerMinute())) {
            return $this->rateLimited();
        }

        RateLimiter::hit($ipKey, 60);

        $emailData = $emailCanonicalizer->canonicalize((string) $request->validated('email'));
        $fingerprint = hash_hmac(
            'sha256',
            $emailData['canonical'],
            $settings->hmacKey($settings->activeHmacKeyVersion()),
        );
        $emailKey = 'promotion:first_purchase:email:'.$fingerprint;

        if (RateLimiter::tooManyAttempts($emailKey, $settings->emailLimitPerHour())) {
            return $this->rateLimited();
        }

        RateLimiter::hit($emailKey, 3600);

        try {
            $result = $issuer->handle(
                email: (string) $request->validated('email'),
                authorizationTextVersion: (string) $request->validated('authorization_text_version'),
                userAgent: $request->userAgent(),
            );
        } catch (Throwable) {
            return $this->unavailable();
        }

        $payload = [
            'status' => 'accepted',
            'delivery' => $result->delivery,
            'message' => $result->message,
            'request_id' => $result->requestId,
        ];

        if ($result->couponCode !== null) {
            $payload['coupon_code'] = $result->couponCode;
        }

        return response()->json($payload, $result->delivery === 'email' ? 202 : 200);
    }

    private function unavailable(): JsonResponse
    {
        return response()->json([
            'status' => 'unavailable',
            'delivery' => 'none',
            'message' => 'A solicitação de cupom está temporariamente indisponível.',
        ], 503);
    }

    private function rateLimited(): JsonResponse
    {
        return response()->json([
            'status' => 'retry_later',
            'delivery' => 'none',
            'message' => 'Não foi possível concluir agora. Tente novamente mais tarde.',
        ], 429);
    }
}
