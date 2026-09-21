<?php

namespace App\Modules\Catalog\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SitemapClientIdentity
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Catalog-Client');
        if ($token === null) {
            return $next($request);
        }
        $key = (string) config('catalog.sitemap_client_key');
        if (! preg_match('/^[a-f0-9]{64}$/iD', $key) ||
            ! preg_match('/^([a-f0-9]{64})\.([0-9]{10})\.([a-f0-9]{64})$/D', $token, $parts) ||
            abs(time() - (int) $parts[2]) > 30 ||
            ! hash_equals(hash_hmac('sha256', 'sitemap-client:v1:'.$parts[1].':'.$parts[2], $key), $parts[3])) {
            return response()->json(['message' => 'Identidade de cliente inválida.'], 403);
        }
        $request->attributes->set('sitemap_client', $parts[1]);

        return $next($request);
    }
}
