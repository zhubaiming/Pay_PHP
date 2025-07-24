<?php

namespace Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\BodyPacker;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class CancelPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new BodyPacker());

        $config = Wechat::getConfig();
        $parameters = $patchwerk->getParameters();

        $patchwerk->mergeParameters([
            '_method' => 'POST',
            '_url' => Wechat::URL[$config['mode']] . '/v3/fund-app/mch-transfer/transfer-bills/' . $parameters['out_bill_no'] . '/cancel',
            '_headers' => ['User-Agent' => ' Payment wechat-pay-v3']
        ]);

        return $next($patchwerk);
    }
}