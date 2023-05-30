<?php

declare(strict_types=1);

namespace MaliBoot\Di;

use MaliBoot\Di\Annotation\InjectAspect;
use MaliBoot\Di\Aop\RegisterInjectPropertyHandler;

class ConfigProvider
{
    public function __invoke(): array
    {
        // Register Property Handler.
        RegisterInjectPropertyHandler::register();

        return [
            'dependencies' => [
            ],
            'aspects' => [
                InjectAspect::class,
            ],
            'annotations' => [
            ],
        ];
    }
}
