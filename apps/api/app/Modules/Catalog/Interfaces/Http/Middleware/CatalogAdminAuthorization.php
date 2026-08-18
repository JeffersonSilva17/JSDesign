<?php

namespace App\Modules\Catalog\Interfaces\Http\Middleware;

use App\Modules\Catalog\Application\Security\AdminIdentityResolver;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CatalogAdminAuthorization
{
    public function __construct(private AdminIdentityResolver $identities) {}

    public function handle(Request $request, Closure $next): Response
    {
        $identity = $this->identities->resolve();

        if ($identity === null) {
            return new JsonResponse([
                'error' => ['code' => 'unauthenticated', 'message_key' => 'auth.unauthenticated'],
            ], 401);
        }

        if (! $identity->can('catalog.manage')) {
            return new JsonResponse([
                'error' => ['code' => 'catalog_forbidden', 'message_key' => 'catalog.auth.forbidden'],
            ], 403);
        }

        $request->attributes->set('catalog_admin_identity', $identity);

        return $next($request);
    }
}
