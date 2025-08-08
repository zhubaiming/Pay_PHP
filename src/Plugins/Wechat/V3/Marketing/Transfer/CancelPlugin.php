<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\JsonPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class CancelPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new JsonPacker());

        $config = Wechat::getConfig();
        $parameters = $patchwerk->getParameters();

        $patchwerk->setParameters([
            '_method' => 'POST',
            '_url' => Wechat::URL[$config['mode']] . '/v3/fund-app/mch-transfer/transfer-bills/out-bill-no/' . $parameters['out_bill_no'] . '/cancel',
        ]);

        return $next($patchwerk);
    }
}