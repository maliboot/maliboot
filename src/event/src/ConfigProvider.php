<?php

declare(strict_types=1);

namespace MaliBoot\Event;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'dependencies' => [
                \Psr\EventDispatcher\EventDispatcherInterface::class => EventDispatcherFactory::class
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
