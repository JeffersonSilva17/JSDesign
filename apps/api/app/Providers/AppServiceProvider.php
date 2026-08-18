<?php

namespace App\Providers;

use App\Modules\Catalog\Application\FileReferenceValidator;
use App\Modules\Catalog\Application\IdGenerator;
use App\Modules\Catalog\Application\Security\AdminIdentityResolver;
use App\Modules\Catalog\Domain\CatalogProductRepository;
use App\Modules\Catalog\Infrastructure\Files\FailClosedFileReferenceValidator;
use App\Modules\Catalog\Infrastructure\Identifiers\LaravelUuidGenerator;
use App\Modules\Catalog\Infrastructure\Persistence\PostgresCatalogProductRepository;
use App\Modules\Catalog\Infrastructure\Security\FailClosedAdminIdentityResolver;
use App\Modules\Promotions\Domain\PromotionCouponRepository;
use App\Modules\Promotions\Infrastructure\Delivery\EmailProvider;
use App\Modules\Promotions\Infrastructure\Delivery\LaravelMailEmailProvider;
use App\Modules\Promotions\Infrastructure\Persistence\PostgresPromotionCouponRepository;
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
        $this->app->bind(PromotionCouponRepository::class, PostgresPromotionCouponRepository::class);
        $this->app->bind(EmailProvider::class, LaravelMailEmailProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
