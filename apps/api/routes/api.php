<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Modules\Promotions\Interfaces\Http\Controllers\FirstPurchaseCouponController;
use App\Modules\Promotions\Interfaces\Http\Controllers\FirstPurchaseOfferController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', HealthController::class)->name('api.v1.health');
    Route::get('/promotions/first-purchase-offer', FirstPurchaseOfferController::class)
        ->name('api.v1.promotions.first-purchase-offer');
    Route::post('/promotions/first-purchase-coupons', FirstPurchaseCouponController::class)
        ->name('api.v1.promotions.first-purchase-coupons');
});
