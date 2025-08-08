<?php

declare(strict_types=1);

namespace Hongyi\Pay\Shortcuts\Wechat;

use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Designer\Plugins\AddRadarPlugin;
use Hongyi\Designer\Plugins\ParserPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Callback\DecryptContentPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Callback\CallbackPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\VerifySignaturePlugin;

class CallbackShortcut implements ShortcutInterface
{
    public static function getPlugins(): array
    {
        return [
            CallbackPlugin::class,
            AddRadarPlugin::class,
            DecryptContentPlugin::class,
            VerifySignaturePlugin::class,
            ParserPlugin::class
        ];
    }
}
