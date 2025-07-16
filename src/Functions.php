<?php

declare(strict_types=1);

use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;

if (!function_exists('get_certificate_content')) {
    function get_certificate_content(?string $path): string
    {
        if (is_file($path)) {
            if (!file_exists($path)) throw new \Exception();

            $content = file_get_contents($path);
        } else {
            $content = $path;
        }

        return $content;
    }
}

if (!function_exists('get_wechat_sign')) {
    function get_wechat_sign(string $certificate, string $signContent): string
    {
        openssl_sign($signContent, $sign, $certificate, OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
    }
}

if (!function_exists('verify_wechat_sign')) {
    function verify_wechat_sign(string $certificate, string $signContent, string $signature): bool
    {
        $result = openssl_verify($signContent, base64_decode($signature), $certificate, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }
}

if (!function_exists('decrypt_wechat_content')) {
    function decrypt_wechat_content(string $cipherText, string $key, string $iv, string $add)
    {
        $block_size = 16;

        $cipherText = base64_decode($cipherText);
        $autoTag = substr($cipherText, $tailLength = 0 - $block_size);

        $plaintext = openssl_decrypt(substr($cipherText, 0, $tailLength), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $autoTag, $add);

        if (false === $plaintext) {
            throw new InvalidConfigException('微信回调解密失败', Exception::CONFIG_ERROR);
        }

        return $plaintext;
    }
}
