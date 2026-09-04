<?php

namespace App\Providers;

use App\Modules\Catalog\Application\FileReferenceValidator;
use App\Modules\Catalog\Application\IdGenerator;
use App\Modules\Catalog\Application\Queries\PublicCatalogImageResolver;
use App\Modules\Catalog\Application\Queries\PublicCatalogQuery;
use App\Modules\Catalog\Application\Queries\PublicCatalogSearchQuery;
use App\Modules\Catalog\Application\Security\AdminIdentityResolver;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Infrastructure\Files\FailClosedFileReferenceValidator;
use App\Modules\Catalog\Infrastructure\Files\FailClosedPublicCatalogImageResolver;
use App\Modules\Catalog\Infrastructure\Files\TestingPublicCatalogImageResolver;
use App\Modules\Catalog\Infrastructure\Identifiers\LaravelUuidGenerator;
use App\Modules\Catalog\Infrastructure\Persistence\PostgresCatalogProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\PostgresPublicCatalogQuery;
use App\Modules\Catalog\Infrastructure\Security\FailClosedAdminIdentityResolver;
use App\Modules\Promotions\Domain\PromotionCouponRepository;
use App\Modules\Promotions\Infrastructure\Delivery\EmailProvider;
use App\Modules\Promotions\Infrastructure\Delivery\LaravelMailEmailProvider;
use App\Modules\Promotions\Infrastructure\Persistence\PostgresPromotionCouponRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CatalogProductRepository::class, PostgresCatalogProductRepository::class);
        $this->app->bind(IdGenerator::class, LaravelUuidGenerator::class);
        $this->app->bind(FileReferenceValidator::class, FailClosedFileReferenceValidator::class);
        $this->app->bind(AdminIdentityResolver::class, FailClosedAdminIdentityResolver::class);
        $this->app->bind(PublicCatalogQuery::class, PostgresPublicCatalogQuery::class);
        $this->app->bind(PublicCatalogSearchQuery::class, PostgresPublicCatalogQuery::class);
        $imageResolver = $this->app->environment('testing') && env('CATALOG_PUBLIC_IMAGE_RESOLVER') === 'e2e'
            ? TestingPublicCatalogImageResolver::class
            : FailClosedPublicCatalogImageResolver::class;
        $this->app->bind(PublicCatalogImageResolver::class, $imageResolver);
        $this->app->bind(PromotionCouponRepository::class, PostgresPromotionCouponRepository::class);
        $this->app->bind(EmailProvider::class, LaravelMailEmailProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('public-catalog', static function (Request $request): Limit {
            $clientIp = $request->getClientIp() ?? 'unknown';

            return Limit::perMinute((int) config('catalog.public_read_rate_limit_per_minute', 240))
                ->by('public-catalog:'.hash('sha256', $clientIp))
                ->response(static fn (Request $request, array $headers) => response()->json([
                    'message' => 'Muitas solicitações. Tente novamente em instantes.',
                    'retry_after' => (int) ($headers['Retry-After'] ?? 60),
                ], 429, $headers)->header('Cache-Control', 'no-store, private'));
        });

        RateLimiter::for('public-catalog-search', static function (Request $request): Limit {
            $clientIp = $request->getClientIp() ?? 'unknown';

            return Limit::perMinute((int) config('catalog.search_rate_limit_per_minute', 60))
                ->by('public-catalog-search:'.hash('sha256', $clientIp))
                ->response(static fn (Request $request, array $headers) => response()->json([
                    'message' => 'Muitas buscas. Tente novamente em instantes.',
                    'retry_after' => (int) ($headers['Retry-After'] ?? 60),
                ], 429, $headers)->header('Cache-Control', 'no-store, private'));
        });
    }
}
