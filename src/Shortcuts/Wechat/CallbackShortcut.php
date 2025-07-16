<?php

declare(strict_types=1);

namespace Hongyi\Pay\Shortcuts\Wechat;

use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Designer\Plugins\ParserPlugin;
use Hongyi\Designer\Plugins\StartPlugin;
use Hongyi\Pay\Plugins\Wechat\BeginPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Callback\DecryptContentPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Callback\MockDestinationPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\VerifySignaturePlugin;

class CallbackShortcut implements ShortcutInterface
{
    public static bool $sendHttp = false;

    public static function getPlugins(): array
    {
        return [
            BeginPlugin::class,
            StartPlugin::class,
            MockDestinationPlugin::class,
            DecryptContentPlugin::class,
            VerifySignaturePlugin::class,
            ParserPlugin::class
        ];
    }
}
