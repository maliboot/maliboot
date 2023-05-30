<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode;

use MaliBoot\ErrorCode\Listener\CollectErrorCodeListener;

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
            ],
            'listeners' => [
                CollectErrorCodeListener::class,
            ],
        ];
    }
}
