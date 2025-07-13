<?php

declare(strict_types=1);

namespace Hongyi\Pay\Shortcuts\Wechat;

use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Pay\Plugins\Wechat\BeginPlugin;
use Hongyi\Designer\Plugins\StartPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Pay\Mini\PayPlugin;
use Hongyi\Designer\Plugins\AddBodyToPayloadPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\AddPayloadSignaturePlugin;
use Hongyi\Designer\Plugins\AddRadarPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Pay\Mini\InvokePlugin;
use Hongyi\Pay\Plugins\Wechat\V3\VerifySignaturePlugin;
use Hongyi\Designer\Plugins\ParserPlugin;

class MiniShortcut implements ShortcutInterface
{
    public static function getPlugins(): array
    {
        return [
            BeginPlugin::class,
            StartPlugin::class,
            PayPlugin::class,
            AddBodyToPayloadPlugin::class,
            AddPayloadSignaturePlugin::class,
            AddRadarPlugin::class,
            InvokePlugin::class,
            VerifySignaturePlugin::class,
            ParserPlugin::class,
        ];
    }
}
