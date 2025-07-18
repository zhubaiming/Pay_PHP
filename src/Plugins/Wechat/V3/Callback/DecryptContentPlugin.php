<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Callback;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Patchwerk;

use Hongyi\Pay\Services\Wechat;
use function decrypt_wechat_content;

class DecryptContentPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $destination = $patchwerk->getDestination();

        if ('encrypt-resource' !== $destination['resource_type']) {
            throw new InvalidConfigException('微信通知资源数据类型错误', Exception::CONFIG_ERROR);
        }

        $resource = $destination['resource'];
        $config = Wechat::getConfig();

        $response = json_decode(decrypt_wechat_content($resource['ciphertext'], $config['mch_api_v3_key'], $resource['nonce'], $resource['associated_data']), true);

        $patchwerk->setDestination($response);

        return $patchwerk;
    }
}
