<?php

namespace App\Modules\Promotions\Infrastructure\Delivery\Jobs;

use App\Modules\Promotions\Domain\PromotionCodeCipher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

final class DeliverFirstPurchaseCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public readonly int $outboxId) {}

    public function handle(PromotionCodeCipher $cipher): void
    {
        $delivery = DB::transaction(function (): ?object {
            $row = DB::table('promotion_outbox')
                ->join('promotion_coupons', 'promotion_coupons.id', '=', 'promotion_outbox.coupon_id')
                ->where('promotion_outbox.id', $this->outboxId)
                ->where('promotion_outbox.state', 'queued')
                ->whereNull('promotion_coupons.revoked_at')
                ->whereNull('promotion_coupons.suppressed_at')
                ->whereNull('promotion_coupons.anonymized_at')
                ->select([
                    'promotion_outbox.id as outbox_id',
                    'promotion_outbox.notification_key',
                    'promotion_outbox.coupon_id',
                    'promotion_coupons.email_for_delivery',
                    'promotion_coupons.code_encrypted',
                    'promotion_coupons.code_key_version',
                ])
                ->lockForUpdate()
                ->first();

            if (! $row) {
                return null;
            }

            DB::table('promotion_outbox')->where('id', $this->outboxId)->update([
                'state' => 'processing',
                'attempts' => DB::raw('attempts + 1'),
                'updated_at' => now(),
            ]);

            return $row;
        });

        if ($delivery === null) {
            return;
        }

        try {
            if (! is_string($delivery->email_for_delivery)
                || ! is_string($delivery->code_encrypted)
                || ! is_string($delivery->code_key_version)) {
                throw new RuntimeException('Registro de entrega promocional incompleto.');
            }

            $code = $cipher->decrypt($delivery->code_encrypted, $delivery->code_key_version);
            $notificationKey = (string) $delivery->notification_key;

            Mail::raw(
                "Seu cupom individual de primeira compra é {$code}. Insira-o manualmente no checkout; a elegibilidade será validada nessa etapa.",
                function ($message) use ($delivery, $notificationKey): void {
                    $message
                        ->to($delivery->email_for_delivery)
                        ->subject('Seu cupom de primeira compra');
                    $message
                        ->getSymfonyMessage()
                        ->getHeaders()
                        ->addTextHeader('X-Promotion-Notification-Key', $notificationKey);
                },
            );

            DB::transaction(function () use ($delivery): void {
                DB::table('promotion_outbox')
                    ->where('id', $this->outboxId)
                    ->where('state', 'processing')
                    ->update([
                        'state' => 'sent',
                        'sent_at' => now(),
                        'last_error' => null,
                        'updated_at' => now(),
                    ]);

                DB::table('promotion_coupons')->where('id', $delivery->coupon_id)->update([
                    'delivery_state' => 'sent',
                    'updated_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            DB::table('promotion_outbox')
                ->where('id', $this->outboxId)
                ->where('state', 'processing')
                ->update([
                    'state' => 'failed_final',
                    'failed_at' => now(),
                    'last_error' => $exception::class,
                    'updated_at' => now(),
                ]);

            return;
        }
    }
}
