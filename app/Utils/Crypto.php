<?php
namespace App\Utils;

class Crypto
{
    public static function encrypt(string $plain, string $keyBase64): string
    {
        $key = base64_decode($keyBase64);
        if ($key === false || strlen($key) < 32) {
            throw new \InvalidArgumentException('CHAVE DE ENCRIPTAÇÃO inválida. Forneça 32 bytes em base64.');
        }

        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) throw new \RuntimeException('Falha na encriptação');

        return base64_encode($iv) . ':' . base64_encode($cipher);
    }

    public static function decrypt(string $enc, string $keyBase64): string
    {
        $key = base64_decode($keyBase64);
        if ($key === false || strlen($key) < 32) {
            throw new \InvalidArgumentException('CHAVE DE ENCRIPTAÇÃO inválida. Forneça 32 bytes em base64.');
        }

        $parts = explode(':', $enc);
        if (count($parts) !== 2) return '';

        $iv = base64_decode($parts[0]);
        $ct = base64_decode($parts[1]);

        $plain = openssl_decrypt($ct, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $plain === false ? '' : $plain;
    }
}
