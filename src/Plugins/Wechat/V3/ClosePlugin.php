<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\JsonPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class ClosePlugin implements PluginInterface
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

        $merges['_url'] = strtr($merges['_url'], [
            '{out_trade_no}' => $patchwerk->getParameters()['out_trade_no']
        ]);

        $patchwerk->setParameters(array_merge([
            '_method' => 'POST',
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
