<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Pay\Refund;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\BodyPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Pay;
use Hongyi\Pay\Services\Wechat;

use function get_config;

class RefundPlugin implements PluginInterface
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
            'notify_url' => $config['refund_notify_url']
        ], $merges));

        return $next($patchwerk);
    }

    private function merchant($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/refund/domestic/refunds'
        ];
    }

    private function partner($config): array
    {
        return [
            '_url' => Wechat::URL[$config['mode']] . '/v3/refund/domestic/refunds',
            'sub_mchid' => $config['sub_mch_id']
        ];
    }
}
