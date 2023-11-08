<?php

declare(strict_types=1);

namespace MaliBoot\FieldCollector;

use MaliBoot\FieldCollector\Listener\CollectFieldListener;

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
//                CollectFieldListener::class,
            ],
            'annotations' => [
            ],
            'publish' => [
            ],
        ];
    }
}
