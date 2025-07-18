<?php

declare(strict_types=1);

namespace Hongyi\Pay;

use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidConfigException;
use Hongyi\Designer\Vaults;
use Hongyi\Pay\Services\Wechat;

class Pay
{
    public const MODE_MERCHANT = 0;
    public const MODE_PARTNER = 1;

    private static ?self $instance = null;

    public function __construct(string $name)
    {
        // 1、将配置文件中的配置，全部注册到Vaults中
        // 2、根据$name获取服务实例
        // 3、通过服务实例的获取快捷通道
        // 4、调用Vaults::shortcut执行快捷通道中包含的所有组件
        $this->getAllConfigs();

//        $this->getAllProviders($name);
    }

    public static function getInstance($name): ?self
    {
        if (self::$instance === null) {
            self::$instance = new self($name);
        }

        return self::$instance;
    }

    public function __call(string $name, array $arguments)
    {
//        return Vaults::get($name, $arguments);
        return new Wechat(...$arguments);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        /*
         * 是否每次返回的都是当前类本身，不重要，重要的是根据 $name 返回对应的执行类，每次都是同一个
         */
        $instance = self::getInstance($name);
        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * @param $name
     * @return void
     */
    private function getAllProviders($name): void
    {
        foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR . '*.php') as $file) {
            if (lcfirst(str_replace('ServiceProvider', '', basename($file, '.php'))) === $name) {
                $this->registerProvider($file);
            }
            break;
        }
    }

    private function registerProvider($file): void
    {
        $className = __NAMESPACE__ . '\\Providers\\' . pathinfo($file, PATHINFO_FILENAME);

        Vaults::registerProvider($className);
    }

    /**
     * @return void
     * @throws InvalidConfigException
     */
    private function getAllConfigs(): void
    {
        $cacheConfigPath = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config.php';

        if (is_readable($cacheConfigPath)) {
            $configPath = $cacheConfigPath;
        } else {
            $configPath = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'pay.php';

            if (!is_readable($configPath)) {
                throw new InvalidConfigException('配置文件不存在或不可读[配置文件应当存在于项目根目录下的`config`文件夹, 并命名为`pay.php`]', Exception::CONFIG_FILE_ERROR);
            }
        }

        Vaults::config(require $configPath);
    }
}