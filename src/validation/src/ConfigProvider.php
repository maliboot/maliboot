<?php

declare(strict_types=1);

namespace MaliBoot\Validation;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'aspects' => [
                \MaliBoot\Validation\Aspect\ValidationAspect::class,
            ],
            'dependencies' => [
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
