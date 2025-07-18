<?php

declare(strict_types=1);

namespace Hongyi\Pay\Shortcuts\Wechat;

use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Designer\Plugins\AddBodyToPayloadPlugin;
use Hongyi\Designer\Plugins\AddRadarPlugin;
use Hongyi\Designer\Plugins\ParserPlugin;
use Hongyi\Designer\Plugins\StartPlugin;
use Hongyi\Pay\Plugins\Wechat\BeginPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\AddPayloadSignaturePlugin;
use Hongyi\Pay\Plugins\Wechat\V3\Marketing\Transfer\BillsPlugin;
use Hongyi\Pay\Plugins\Wechat\V3\VerifySignaturePlugin;

class TransferShortcut implements ShortcutInterface
{
    public static function getPlugins(): array
    {
        return [
            BeginPlugin::class,
            StartPlugin::class,
            BillsPlugin::class,
            AddBodyToPayloadPlugin::class,
            AddPayloadSignaturePlugin::class,
            AddRadarPlugin::class,
            VerifySignaturePlugin::class,
            ParserPlugin::class
        ];
    }
}