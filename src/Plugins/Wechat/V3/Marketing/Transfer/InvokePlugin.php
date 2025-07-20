<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer;

use Closure;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\InvalidResponseException;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Enums\Wechat\TransferStateEnum;
use Hongyi\Pay\Services\Wechat;

class InvokePlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $destination = $patchwerk->getDestination();

        if (in_array($destination['state'], TransferStateEnum::isFail())) {
            throw new InvalidResponseException(TransferStateEnum::{$destination['state']}->name());
        }

        $config = Wechat::getConfig();

        $response = [
            'response' => $destination,
            'jsapi' => [
                'mchId' => $config['mch_id'],
                'appId' => $config['mini_app_id'],
                'package' => $patchwerk->getDestination()['package_info']
            ]
        ];

        $patchwerk->setDestination($response);

        return $patchwerk;
    }
}