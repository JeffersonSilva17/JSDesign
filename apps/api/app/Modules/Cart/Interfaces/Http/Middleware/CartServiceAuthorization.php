<?php

namespace App\Modules\Cart\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CartServiceAuthorization
{
    public function handle(Request $request, Closure $next): Response
    {
        $configured = (string) config('services.cart.internal_token', '');
        $authorization = (string) $request->header('Authorization', '');
        if ($configured === '' || ! hash_equals('Bearer '.$configured, $authorization)) {
            return new JsonResponse(['error' => ['code' => 'temporarily_unavailable', 'message' => 'Request unavailable.']], 503, ['Cache-Control' => 'no-store, private']);
        }

        return $next($request);
    }
}
