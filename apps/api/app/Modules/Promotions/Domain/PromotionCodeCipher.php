<?php

namespace App\Modules\Promotions\Domain;

use Illuminate\Encryption\Encrypter;
use RuntimeException;

final readonly class PromotionCodeCipher
{
    public function __construct(private FirstPurchasePromotionSettings $settings) {}

    /**
     * @return array{encrypted:string,key_version:string}
     */
    public function encrypt(string $plainText): array
    {
        $version = $this->settings->activeEncryptionKeyVersion();

        return [
            'encrypted' => $this->encrypter($version)->encryptString($plainText),
            'key_version' => $version,
        ];
    }

    public function decrypt(string $encrypted, string $keyVersion): string
    {
        return $this->encrypter($keyVersion)->decryptString($encrypted);
    }

    private function encrypter(string $version): Encrypter
    {
        $rawKey = $this->settings->encryptionKey($version);
        $key = $this->decodeKey($rawKey);

        if (strlen($key) !== 32) {
            throw new RuntimeException("Chave de criptografia promocional inválida para a versão {$version}.");
        }

        return new Encrypter($key, 'AES-256-CBC');
    }

    private function decodeKey(string $rawKey): string
    {
        if (str_starts_with($rawKey, 'base64:')) {
            return base64_decode(substr($rawKey, 7), true) ?: '';
        }

        $decoded = base64_decode($rawKey, true);

        return $decoded !== false && strlen($decoded) === 32 ? $decoded : $rawKey;
    }
}
