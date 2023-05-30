<?php

declare(strict_types=1);

namespace MaliBoot\PluginConfig;

use Hyperf\Contract\ConfigInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'dependencies' => [
                ConfigInterface::class => ConfigFactory::class,
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
