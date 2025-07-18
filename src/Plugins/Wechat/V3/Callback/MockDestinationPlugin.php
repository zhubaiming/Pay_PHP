<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat\V3\Callback;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Packers\BodyPacker;
use Hongyi\Designer\Patchwerk;

class MockDestinationPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setPacker(new BodyPacker());

        $parameters = $patchwerk->getParameters();

        $patchwerk->setDestination(clone $parameters['response'])
            ->setDestinationOrigin(clone $parameters['response']);

        return $next($patchwerk);
    }
}
