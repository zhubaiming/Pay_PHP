<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\JsonPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class BillsPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new JsonPacker());

        $config = Wechat::getConfig();

        $patchwerk->mergeParameters([
            '_method' => 'POST',
            '_url' => Wechat::URL[$config['mode']] . '/v3/fund-app/mch-transfer/transfer-bills',
            'appid' => $config['mini_app_id'],
            'notify_url' => $config['transfer_notify_url']
        ]);

        return $next($patchwerk);
    }
}
