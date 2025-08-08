<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Pay\Mini;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\JsonPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class PayPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new JsonPacker());

        $config = Wechat::getConfig();

        $merges = match ($config['mode']) {
            Wechat::MODE_MERCHANT => $this->merchant($config),
            Wechat::MODE_PARTNER => $this->partner($config),
            default => []
        };

        $patchwerk->mergeParameters(array_merge([
            '_method' => 'POST',
            'notify_url' => $config['pay_notify_url']
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
