<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;
use Random\RandomException;

use function random_nonce;
use function get_radar_method;
use function get_certificate_content;
use function get_wechat_sign;

/**
 * 生成微信支付签名插件
 *
 * @throws InvalidConfigException
 * @throws RandomException
 * @throws InvalidConfigException
 * @throws \Exception
 */
class AddPayloadSignaturePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();
        $timestamp = time();
        $nonce_str = random_nonce();
        $signContent = $this->getSignatureContent($parameters, $timestamp, $nonce_str);

        $config = Wechat::getConfig();
        $signature = $this->getSignature($config, $timestamp, $nonce_str, $signContent);

        $patchwerk->mergeParameters(['_authorization' => $signature, '_headers' => array_merge($parameters['_headers'] ?? [], ['Wechatpay-Serial' => $config['public_key_id']])]);

        return $next($patchwerk);
    }

    private function getSignatureContent($parameters, $timestamp, $nonce_str): string
    {
        $url_parse = parse_url($parameters['_url']);

        return get_radar_method($parameters) . "\n" .
            $url_parse['path'] . (isset($url_parse['query']) ? '?' . $url_parse['query'] : '') . "\n{$timestamp}\n{$nonce_str}\n{$parameters['_body']}\n";
    }

    private function getSignature($config, $timestamp, $nonce_str, $signContent): string
    {
        if (empty($mchPublicCertPath = $config['mch_public_cert_path'] ?? null) || empty($mchSecretCertPath = $config['mch_secret_cert_path'] ?? null)) {
            throw new InvalidConfigException('配置异常: 缺少商户API证书文件配置', Exception::CONFIG_FILE_ERROR);
        }

        $ssl = openssl_x509_parse(get_certificate_content($mchPublicCertPath));

        if (empty($ssl['serialNumberHex'])) {
            throw new InvalidConfigException('解析 商户API证书文件 出错', Exception::CONFIG_FILE_ERROR);
        }

        $auth = sprintf(
            'mchid="%s",serial_no="%s",timestamp="%d",nonce_str="%s",signature="%s"',
            $config['mch_id'],
            $ssl['serialNumberHex'],
            $timestamp,
            $nonce_str,
            get_wechat_sign(get_certificate_content($mchSecretCertPath), $signContent)
        );

        return 'WECHATPAY2-SHA256-RSA2048 ' . $auth;
    }
}
