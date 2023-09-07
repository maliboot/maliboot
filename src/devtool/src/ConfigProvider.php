<?php

namespace MaliBoot\Devtool;

use Hyperf\Server\Event;
use Hyperf\Server\Server;
use Swoole\Constant;
use Hyperf\HttpServer\Router\Router;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'aspects' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ]
        ];
    }
}