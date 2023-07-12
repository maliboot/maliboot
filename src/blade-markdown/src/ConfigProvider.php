<?php

declare(strict_types=1);

namespace MaliBoot\BladeMarkdown;

use MaliBoot\BladeMarkdown\Contract\MarkdownRenderInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                MarkdownRenderInterface::class => MarkdownFactory::class,
            ],
            'aspects' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'listeners' => [
            ],
            'view' => [
                'components' => [
                    'markdown' => MarkdownComponent::class,
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The markdown for blade.',
                    'source' => __DIR__ . '/../publish/view_blade_markdown.php',
                    'destination' => BASE_PATH . '/config/autoload/view_blade_markdown.php',
                ],
            ],
        ];
    }
}
