<?php

declare(strict_types=1);

namespace MaliBoot\Response;

use Hyperf\HttpServer\Response;
use MaliBoot\Response\Contract\ResponseInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ResponseInterface::class => Response::class,
            ],
        ];
    }
}
