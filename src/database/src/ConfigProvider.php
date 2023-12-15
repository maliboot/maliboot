<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use MaliBoot\Database\Listener\DbQueryExecutedDebugListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'aspects' => [
                \MaliBoot\Database\Aspect\InjectAspect::class,
            ],
            'commands' => [
            ],
            'dependencies' => [
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
