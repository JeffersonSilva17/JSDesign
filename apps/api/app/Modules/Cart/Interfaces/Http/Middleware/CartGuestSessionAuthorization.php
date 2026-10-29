<?php

namespace App\Modules\Cart\Interfaces\Http\Middleware;

use App\Modules\Cart\Application\GuestCartSession;
use App\Modules\Cart\Domain\CartFailure;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CartGuestSessionAuthorization
{
    public function __construct(private GuestCartSession $sessions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $reference = (string) $request->header('X-Guest-Session', '');
        if (preg_match('/^[A-Za-z0-9_-]{43}$/', $reference) !== 1) {
            return $this->error('temporarily_unavailable', 503);
        }
        try {
            $request->attributes->set('cart_owner_hash', $this->sessions->authorize($reference, $request->header('X-CSRF-Token'), $request->isMethod('POST')));
            $request->attributes->set('cart_session_reference', $reference);

            return $next($request);
        } catch (CartFailure $failure) {
            return $this->error($failure->publicCode, $failure->httpStatus);
        }
    }

    private function error(string $code, int $status): JsonResponse
    {
        return response()->json(['error' => ['code' => $code, 'message' => 'Request unavailable.']], $status)->header('Cache-Control', 'no-store, private');
    }
}
