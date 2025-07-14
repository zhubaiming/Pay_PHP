<?php

declare(strict_types=1);

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