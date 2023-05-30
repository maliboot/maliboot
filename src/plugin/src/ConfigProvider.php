<?php

declare(strict_types=1);

namespace MaliBoot\Plugin;

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
                \MaliBoot\Plugin\Listener\BindImplListener::class,
            ],
            'annotations' => [
            ],
            'publish' => [
            ],
        ];
    }
}
