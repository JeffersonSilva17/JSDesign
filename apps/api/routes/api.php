<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Modules\Catalog\Interfaces\Http\Controllers\CreateCatalogProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\GetPublicCatalogFacetsController;
use App\Modules\Catalog\Interfaces\Http\Controllers\GetPublishedCatalogProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\ListPublicCatalogProductsController;
use App\Modules\Catalog\Interfaces\Http\Controllers\PublishCatalogProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\SearchPublicCatalogController;
use App\Modules\Catalog\Interfaces\Http\Controllers\UnpublishCatalogProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\UpdateCatalogProductController;
use App\Modules\Catalog\Interfaces\Http\Middleware\CatalogAdminAuthorization;
use App\Modules\Catalog\Interfaces\Http\Middleware\PublicCatalogNoStore;
use App\Modules\Promotions\Interfaces\Http\Controllers\FirstPurchaseCouponController;
use App\Modules\Promotions\Interfaces\Http\Controllers\FirstPurchaseOfferController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', HealthController::class)->name('api.v1.health');
    Route::get('/promotions/first-purchase-offer', FirstPurchaseOfferController::class)
        ->name('api.v1.promotions.first-purchase-offer');
    Route::post('/promotions/first-purchase-coupons', FirstPurchaseCouponController::class)
        ->name('api.v1.promotions.first-purchase-coupons');

    Route::prefix('catalog')->middleware([PublicCatalogNoStore::class, 'throttle:public-catalog'])->group(function (): void {
        Route::get('/products', ListPublicCatalogProductsController::class)->name('api.v1.catalog.products.index');
        Route::get('/products/{slug}', GetPublishedCatalogProductController::class)
            ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('api.v1.catalog.products.show');
        Route::get('/facets', GetPublicCatalogFacetsController::class)->name('api.v1.catalog.facets.index');
    });

    Route::get('/catalog/search', SearchPublicCatalogController::class)
        ->middleware([PublicCatalogNoStore::class, 'throttle:public-catalog-search'])
        ->name('api.v1.catalog.search');

    Route::prefix('admin/catalog/products')
        ->middleware(CatalogAdminAuthorization::class)
        ->group(function (): void {
            Route::post('/', CreateCatalogProductController::class)->name('api.v1.admin.catalog.products.store');
            Route::patch('/{product}', UpdateCatalogProductController::class)
                ->whereUuid('product')
                ->name('api.v1.admin.catalog.products.update');
            Route::post('/{product}/publish', PublishCatalogProductController::class)
                ->whereUuid('product')
                ->name('api.v1.admin.catalog.products.publish');
            Route::post('/{product}/unpublish', UnpublishCatalogProductController::class)
                ->whereUuid('product')
                ->name('api.v1.admin.catalog.products.unpublish');
        });
});
