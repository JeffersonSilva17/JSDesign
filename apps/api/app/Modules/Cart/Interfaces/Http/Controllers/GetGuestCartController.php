<?php

namespace App\Modules\Cart\Interfaces\Http\Controllers;

use App\Modules\Cart\Application\GetGuestCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class GetGuestCartController
{
    public function __construct(private GetGuestCart $cart) {}

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(['items' => $this->cart->execute((string) $request->attributes->get('cart_owner_hash'))])
            ->header('Cache-Control', 'no-store, private');
    }
}
