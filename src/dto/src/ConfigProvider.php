<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Aspect\InjectAspect;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'aspects' => [
                InjectAspect::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
