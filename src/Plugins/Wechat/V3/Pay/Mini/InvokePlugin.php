<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Pay\Mini;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Patchwerk;

use Hongyi\Pay\Services\Wechat;
use function random_nonce;
use function get_certificate_content;
use function get_wechat_sign;

class InvokePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $prepayId = $patchwerk->getDestination()['prepay_id'];
        $timestamp = time();
        $nonce_str = random_nonce(32);

        $signContent = $this->getSignatureContent($patchwerk->getParameters(), $timestamp, $nonce_str, $prepayId);

        $signature = $this->getSignature($timestamp, $nonce_str, $prepayId, $signContent);

        $patchwerk->setDestination($signature);

        return $patchwerk;
    }

    private function getSignatureContent($parameters, $timestamp, $nonce_str, $prepay_id): string
    {
        return "{$parameters['appid']}\n{$timestamp}\n{$nonce_str}\nprepay_id={$prepay_id}\n";
    }

    private function getSignature($timestamp, $nonce_str, $prepay_id, $signContent): array
    {
        $config = Wechat::getConfig();
        if (empty($mchPublicCertPath = $config['mch_public_cert_path'] ?? null) || empty($mchSecretCertPath = $config['mch_secret_cert_path'] ?? null)) {
            throw new InvalidConfigException('配置异常: 缺少商户API证书文件配置', Exception::CONFIG_FILE_ERROR);
        }

        $ssl = openssl_x509_parse(get_certificate_content($mchPublicCertPath));

        if (empty($ssl['serialNumberHex'])) {
            throw new InvalidConfigException('配置异常: 解析 商户API证书文件 出错', Exception::CONFIG_FILE_ERROR);
        }

        return [
            'timeStamp' => strval($timestamp),
            'nonceStr' => $nonce_str,
            'package' => 'prepay_id=' . $prepay_id,
            'signType' => 'RSA',
            'paySign' => get_wechat_sign(get_certificate_content($mchSecretCertPath), $signContent)
        ];
    }
}
