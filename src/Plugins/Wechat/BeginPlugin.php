<?php

declare(strict_types=1);

namespace Hongyi\Pay\Plugins\Wechat;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;
use Hongyi\Pay\Enums\Wechat\HttpEnum;

class BeginPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk->setHttpEnum(HttpEnum::OK);

        return $next($patchwerk);
    }
}