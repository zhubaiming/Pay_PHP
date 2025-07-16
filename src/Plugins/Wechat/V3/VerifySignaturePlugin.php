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

//        if (300 >= intval(abs(intval(bcsub(Formatter::timestamp(), $wechatpayTimestamp, 0)))))

        $originBody = $originDestination->getBody()->getContents();

        return "{$wechatpayTimestamp}\n{$wechatpayNonce}\n{$originBody}\n";
    }

    private function verifySignature($originDestination, $signContent): void
    {
        $config = get_config('wechat');
        $wechatpaySignature = $originDestination->getHeaderLine('Wechatpay-Signature');
//        $wechatpaySerial = $originDestination->getHeaderLine('Wechatpay-Serial');



        /*
         * // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = ;
         */

        if (empty($mchPublicKeyPath = $config['mch_public_key_path'] ?? null)) {
            throw new InvalidConfigException('配置异常: 缺少商户API密钥文件配置', Exception::CONFIG_FILE_ERROR);
        }

        if (!verify_wechat_sign(get_certificate_content($mchPublicKeyPath), $signContent, $wechatpaySignature)) {
            throw new InvalidConfigException('微信验证应答签名失败', Exception::CONFIG_ERROR);
        }
    }
}