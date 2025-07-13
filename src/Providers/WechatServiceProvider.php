<?php

declare(strict_types=1);

namespace Hongyi\Pay\Providers;

use Hongyi\Designer\Contracts\ServiceProviderInterface;
use Hongyi\Designer\Vaults;
use Hongyi\Pay\Services\Wechat;

class WechatServiceProvider implements ServiceProviderInterface
{
    public function register(mixed $data = null): void
    {
        Vaults::set('wecom', new Wechat());
    }
}
