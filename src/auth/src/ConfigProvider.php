<?php

declare(strict_types=1);

namespace MaliBoot\Auth;

use MaliBoot\Auth\Aspect\AuthAspect;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \MaliBoot\Contract\Auth\AuthFactory::class => AuthFactory::class,
            ],
            'commands' => [
            ],
            'aspects' => [
                AuthAspect::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'auth',
                    'description' => 'auth 组件配置.', // 描述
                    'source' => __DIR__ . '/../publish/auth.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/auth.php', // 复制为这个路径下的该文件
                ],
            ],
        ];
    }
}
