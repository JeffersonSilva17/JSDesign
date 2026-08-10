<?php

namespace App\Modules\Promotions\Infrastructure\Delivery;

use App\Modules\Promotions\Infrastructure\Delivery\Jobs\DeliverFirstPurchaseCoupon;
use Illuminate\Support\Facades\DB;
use Throwable;

final class LaravelMailEmailProvider implements EmailProvider
{
    public function queueFirstPurchaseCoupon(int $couponId): void
    {
        $outboxId = DB::transaction(function () use ($couponId): ?int {
            $outbox = DB::table('promotion_outbox')
                ->where('coupon_id', $couponId)
                ->where('channel', 'email')
                ->where('state', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $outbox) {
                return null;
            }

            DB::table('promotion_outbox')->where('id', $outbox->id)->update([
                'state' => 'queued',
                'updated_at' => now(),
            ]);

            DB::table('promotion_coupons')->where('id', $couponId)->update([
                'delivery_state' => 'queued',
                'updated_at' => now(),
            ]);

            return (int) $outbox->id;
        });

        if ($outboxId === null) {
            return;
        }

        try {
            DeliverFirstPurchaseCoupon::dispatch($outboxId)->afterCommit();
        } catch (Throwable $exception) {
            DB::table('promotion_outbox')
                ->where('id', $outboxId)
                ->where('state', 'queued')
                ->update(['state' => 'pending', 'updated_at' => now()]);

            throw $exception;
        }
    }
}
