<?php

declare(strict_types=1);

namespace MaliBoot\Framework;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'dependencies' => [
            ],
            'listeners' => [
            ],
            'annotations' => [
            ],
            'publish' => [
            ],
        ];
    }
}
