<?php

namespace App\Modules\Promotions\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Promotions\Domain\FirstPurchasePromotionSettings;
use Illuminate\Http\JsonResponse;

final class FirstPurchaseOfferController extends Controller
{
    public function __invoke(FirstPurchasePromotionSettings $settings): JsonResponse
    {
        return response()->json($settings->publicOffer());
    }
}
