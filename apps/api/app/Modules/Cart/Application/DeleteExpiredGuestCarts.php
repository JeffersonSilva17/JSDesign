<?php

namespace App\Modules\Cart\Application;

use Illuminate\Support\Facades\DB;

final class DeleteExpiredGuestCarts
{
    public function execute(): void
    {
        DB::transaction(function (): void {
            DB::table('cart_idempotency_keys')->where('expires_at', '<=', now())->delete();
            DB::table('guest_carts')->where('expires_at', '<=', now())->delete();
            DB::table('guest_cart_sessions')->where('expires_at', '<=', now())->delete();
        });
    }
}
