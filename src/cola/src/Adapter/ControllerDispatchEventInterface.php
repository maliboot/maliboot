<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Adapter;

interface ControllerDispatchEventInterface
{
    public static function dispatchBefore(
        \Hyperf\HttpServer\CoreMiddleware $coreMiddleware,
        string $controller,
        string $action,
        array $arguments
    );
}
