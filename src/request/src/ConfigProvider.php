<?php

declare(strict_types=1);

namespace MaliBoot\Request;

use Hyperf\HttpServer\Request;
use MaliBoot\Request\Contract\RequestInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                RequestInterface::class => Request::class,
            ],
        ];
    }
}
