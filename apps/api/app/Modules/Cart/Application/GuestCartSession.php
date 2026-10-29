<?php

namespace App\Modules\Cart\Application;

use App\Modules\Cart\Domain\CartFailure;
use Illuminate\Support\Facades\DB;

final class GuestCartSession
{
    /** @return array{reference:string,csrf_token:string,idempotency_key:string} */
    public function issue(?string $reference): array
    {
        return DB::transaction(function () use ($reference): array {
            $current = $reference === null ? null : DB::table('guest_cart_sessions')->where('reference', $reference)->lockForUpdate()->first();
            $reusable = $current !== null && now()->lt($current->expires_at);
            $reference = $reusable ? $reference : $this->token();
            $csrf = $this->token();
            $idempotencyKey = $this->token();
            $values = [
                'csrf_hash' => hash('sha256', $csrf), 'idempotency_key_hash' => hash('sha256', $idempotencyKey),
                'payload_hash' => null, 'expires_at' => now()->addDays(30), 'updated_at' => now(),
            ];
            if (! $reusable) {
                if ($current !== null) {
                    DB::table('guest_cart_sessions')->where('reference', $current->reference)->delete();
                }
                $values += ['reference' => $reference, 'owner_hash' => hash('sha256', $this->token()), 'created_at' => now()];
                DB::table('guest_cart_sessions')->insert($values);
            } else {
                DB::table('guest_cart_sessions')->where('reference', $reference)->update($values);
            }

            return ['reference' => $reference, 'csrf_token' => $csrf, 'idempotency_key' => $idempotencyKey];
        });
    }

    public function authorize(string $reference, ?string $csrfToken, bool $requiresCsrf): string
    {
        $session = DB::table('guest_cart_sessions')->where('reference', $reference)->where('expires_at', '>', now())->first();
        if ($session === null) {
            throw new CartFailure('temporarily_unavailable', 503);
        }
        if ($requiresCsrf && (! is_string($csrfToken) || ! hash_equals((string) $session->csrf_hash, hash('sha256', $csrfToken)))) {
            throw new CartFailure('csrf_failed', 419);
        }

        return (string) $session->owner_hash;
    }

    /** @param array<string,mixed> $input */
    public function bindSubmission(string $reference, string $key, array $input): void
    {
        $payloadHash = CanonicalJson::hash($input);
        DB::transaction(function () use ($reference, $key, $payloadHash): void {
            $session = DB::table('guest_cart_sessions')->where('reference', $reference)->where('expires_at', '>', now())->lockForUpdate()->first();
            if ($session === null) {
                throw new CartFailure('temporarily_unavailable', 503);
            }
            if (! hash_equals((string) $session->idempotency_key_hash, hash('sha256', $key))) {
                throw new CartFailure('idempotency_conflict', 409);
            }
            if ($session->payload_hash !== null && ! hash_equals((string) $session->payload_hash, $payloadHash)) {
                throw new CartFailure('idempotency_conflict', 409);
            }
            if ($session->payload_hash === null) {
                DB::table('guest_cart_sessions')->where('reference', $reference)->update(['payload_hash' => $payloadHash, 'updated_at' => now()]);
            }
        });
    }

    private function token(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
