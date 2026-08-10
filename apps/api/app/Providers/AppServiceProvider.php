<?php

namespace App\Providers;

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
