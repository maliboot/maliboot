<?php

declare(strict_types=1);

namespace MaliBoot\Cola;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'aspects' => [
                \MaliBoot\Cola\Aspect\InjectAspect::class
            ],
            'dependencies' => [
                \Hyperf\HttpServer\Router\DispatcherFactory::class => \MaliBoot\Cola\Adapter\DispatcherFactory::class,
                \Hyperf\HttpServer\CoreMiddleware::class => \MaliBoot\Cola\Adapter\CoreMiddleware::class,
            ],
            'listeners' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
            ],
        ];
    }
}
