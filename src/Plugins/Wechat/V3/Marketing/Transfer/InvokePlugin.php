<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer;

use Closure;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Services\Wechat;

class InvokePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $config = Wechat::getConfig();

        $response = [
            'mchId' => $config['mch_id'],
            'appId' => $config['mini_app_id'],
            'package' => $patchwerk->getDestination()['package_info']
        ];

        $patchwerk->setDestination($response);

        return $patchwerk;
    }
}