<?php

declare(strict_types=1);

namespace MaliBoot\Request;

use MaliBoot\Request\Contract\RequestInterface as MaliRequestInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                MaliRequestInterface::class => Request::class,
                RequestInterface::class => Request::class,
            ],
        ];
    }
}
