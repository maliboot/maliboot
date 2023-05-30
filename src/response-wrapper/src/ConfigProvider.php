<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

use MaliBoot\ResponseWrapper\Contract\ResponseWrapperInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'dependencies' => [
                ResponseWrapperInterface::class => ResponseWrapper::class,
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
