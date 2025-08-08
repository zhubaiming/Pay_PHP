<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Callback;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Direction\NoHttpRequestDirection;
use Hongyi\Designer\Packers\JsonPacker;
use Hongyi\Designer\Patchwerk;

class CallbackPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new JsonPacker());
        $patchwerk->setDirection(NoHttpRequestDirection::class);

        $parameters = $patchwerk->getParameters();

        $patchwerk->setPayload($parameters['body']);

        $patchwerk->mergeParameters([
            '_method' => 'GET',
            '_url' => '/',
            '_headers' => $parameters['headers'],
            '_body' => $parameters['body']
        ]);

        return $next($patchwerk);
    }
}
