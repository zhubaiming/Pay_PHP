<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

use function get_certificate_content;

/**
 * 验证微信应答签名插件
 *
 * @throws InvalidConfigException
 */
class VerifySignaturePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        // 检查通知时间偏移量，允许5分钟(300秒)之内的偏移
        $timeOffsetStatus = 300;

        $signContent = $this->getSignatureContent($patchwerk->getDestinationOrigin(), $timeOffsetStatus);

        $this->verifySignature($patchwerk->getDestinationOrigin(), $signContent);

        return $patchwerk;
    }

    private function getSignatureContent($originDestination, $timeOffsetStatus): string
    {
        $wechatpayNonce = $originDestination->getHeaderLine('Wechatpay-Nonce');
        $wechatpayTimestamp = $originDestination->getHeaderLine('Wechatpay-Timestamp');

        if ($timeOffsetStatus < abs(intval(bcsub(strval(time()), $wechatpayTimestamp, 0)))) throw new InvalidConfigException('微信验证应答签名失败: 应答时间超过时效', Exception::CONFIG_FILE_ERROR);

        $originBody = $originDestination->getBody()->getContents();

        return "{$wechatpayTimestamp}\n{$wechatpayNonce}\n{$originBody}\n";
    }

    private function verifySignature($originDestination, $signContent): void
    {
        $config = Wechat::getConfig();
        $wechatpaySignature = $originDestination->getHeaderLine('Wechatpay-Signature');
        $wechatpaySerial = $originDestination->getHeaderLine('Wechatpay-Serial');

        if ($config['public_key_id'] !== $wechatpaySerial) {
            throw new InvalidConfigException('微信验证应答签名失败: 商户支付公钥证书错误', Exception::CONFIG_ERROR);
        }

        if (empty($mchPublicKeyPath = $config['mch_public_key_path'] ?? null)) {
            throw new InvalidConfigException('配置异常: 缺少商户API密钥文件配置', Exception::CONFIG_FILE_ERROR);
        }

        if (!verify_wechat_sign(get_certificate_content($mchPublicKeyPath), $signContent, $wechatpaySignature)) {
            throw new InvalidConfigException('微信验证应答签名失败: 签名错误', Exception::CONFIG_ERROR);
        }
    }
}
