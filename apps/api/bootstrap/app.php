<?php

use App\Modules\Catalog\Domain\CatalogConflict;
use App\Modules\Catalog\Domain\CatalogValidationFailed;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = array_values(array_filter(array_map(
            static fn (string $proxy): string => trim($proxy),
            explode(',', (string) env('TRUSTED_PROXIES', '')),
        )));

        if ($trustedProxies !== []) {
            $middleware->trustProxies(
                at: $trustedProxies,
                headers: Request::HEADER_X_FORWARDED_FOR
                    | Request::HEADER_X_FORWARDED_HOST
                    | Request::HEADER_X_FORWARDED_PORT
                    | Request::HEADER_X_FORWARDED_PROTO,
            );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (CatalogValidationFailed $exception, HttpRequest $request) {
            return response()->json(['errors' => $exception->errors], 422);
        });
        $exceptions->render(function (CatalogConflict $exception, HttpRequest $request) {
            return response()->json([
                'error' => [
                    'code' => $exception->errorCode,
                    'message_key' => 'catalog.conflict.'.$exception->errorCode,
                ],
            ], $exception->errorCode === 'product_not_found' ? 404 : 409);
        });
        $exceptions->render(function (NotFoundHttpException $exception, HttpRequest $request) {
            if ($request->is('api/v1/catalog/*')) {
                return response()->json(['message' => 'Produto não encontrado.'], 404)
                    ->header('Cache-Control', 'no-store, private');
            }

            if (! $request->is('api/v1/admin/catalog/*')) {
                return null;
            }

            return response()->json([
                'error' => [
                    'code' => 'not_found',
                    'message_key' => 'catalog.not_found',
                ],
            ], 404);
        });
    })->create();
