<?php

declare(strict_types=1);

namespace Hongyi\Pay\Services;

use Hongyi\Designer\Vaults;

use function get_config;
use function get_parent_namespace;

class Wechat
{
    public const MODE_MERCHANT = 0;
    public const MODE_PARTNER = 1;

    public const URL = [
        self::MODE_MERCHANT => 'https://api.mch.weixin.qq.com',
        self::MODE_PARTNER => 'https://api.mch.weixin.qq.com'
    ];

    public static array $config;

    public function __construct(string $type = 'default')
    {
        self::$config = get_config('wechat', $type);
    }

    public function __call(string $name, array $arguments)
    {
        $shortcut = get_parent_namespace(__NAMESPACE__) . '\\Shortcuts\\Wechat\\' . ucfirst($name) . 'Shortcut';

        return Vaults::shortcut($shortcut, ...$arguments);
    }

    public static function getConfig(): array
    {
        return self::$config;
    }
}
