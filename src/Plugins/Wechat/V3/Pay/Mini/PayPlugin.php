<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Pay\Mini;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\BodyPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Pay;
use Hongyi\Pay\Services\Wechat;

use function get_config;

class PayPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new BodyPacker());

        $config = get_config('wechat');

        $merges = match ($config['mode']) {
            Pay::MODE_MERCHANT => $this->merchant($config),
            Pay::MODE_PARTNER => $this->partner($config),
            default => []
        };

        $patchwerk->mergeParameters(array_merge([
            '_method' => 'POST',
            '_headers' => ['User-Agent' => ' Payment wechat-pay-v3'],
            'notify_url' => 'https://develop.wangxingren.fun/wechat_notify/pay/payment/jsapi'
        ], $merges));

        return $next($patchwerk);
    }

    private function merchant($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/pay/transactions/jsapi',
            'appid' => $config['mini_app_id'],
            'mchid' => $config['mch_id']
        ];
    }

    private function partner($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/pay/partner/transactions/jsapi',
            'sp_appid' => $config['mini_app_id'],
            'sp_mchid' => $config['mch_id'],
            'sub_mchid' => $config['sub_mch_id']
        ];
    }
}
