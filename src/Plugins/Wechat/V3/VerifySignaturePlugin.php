<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3;

use Closure;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Patchwerk;

use function get_config;
use function get_certificate_content;

class VerifySignaturePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $signContent = $this->getSignatureContent($patchwerk->getDestinationOrigin());

        $this->verifySignature($patchwerk->getDestinationOrigin(), $signContent);

        return $patchwerk;
    }

    private function getSignatureContent($originDestination): string
    {
        $wechatpayNonce = $originDestination->getHeaderLine('Wechatpay-Nonce');
        $wechatpayTimestamp = $originDestination->getHeaderLine('Wechatpay-Timestamp');

        $originBody = $originDestination->getBody()->getContents();

        return "{$wechatpayTimestamp}\n{$wechatpayNonce}\n{$originBody}\n";
    }

    private function verifySignature($originDestination, $signContent): void
    {
        $config = get_config('wechat');
        $wechatpaySignature = $originDestination->getHeaderLine('Wechatpay-Signature');
//        $wechatpaySerial = $originDestination->getHeaderLine('Wechatpay-Serial');

        if (!verify_wechat_sign(
            get_certificate_content($config['mch_public_key_path']),
            $signContent,
            $wechatpaySignature
        )) {
            throw new InvalidConfigException('微信验证应答签名失败', Exception::CONFIG_ERROR);
        }
    }
}