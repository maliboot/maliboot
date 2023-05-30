<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use MaliBoot\Database\Listener\DbQueryExecutedDebugListener;

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
                DbQueryExecutedDebugListener::class,
            ],
            'annotations' => [
            ],
            'publish' => [
            ],
        ];
    }
}
