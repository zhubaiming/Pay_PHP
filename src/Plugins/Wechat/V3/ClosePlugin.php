<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\BodyPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Pay;
use Hongyi\Pay\Services\Wechat;
use function get_config;

class ClosePlugin implements PluginInterface
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

        $merges['_url'] = strtr($merges['_url'], [
            '{out_trade_no}' => $patchwerk->getParameters()['out_trade_no']
        ]);

        $patchwerk->setParameters(array_merge([
            '_method' => 'POST',
            '_headers' => ['User-Agent' => 'wechat-pay-v3'],
        ], $merges));

        return $next($patchwerk);
    }

    private function merchant($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/pay/transactions/out-trade-no/{out_trade_no}/close',
            'mchid' => $config['mch_id']
        ];
    }

    private function partner($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/pay/partner/transactions/out-trade-no/{out_trade_no}/close',
            'sp_mchid' => $config['mch_id'],
            'sub_mchid' => $config['sub_mch_id']
        ];
    }
}