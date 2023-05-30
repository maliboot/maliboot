<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \MaliBoot\Contract\Hashing\Hasher::class => HasherFactory::class,
            ],
        ];
    }
}
