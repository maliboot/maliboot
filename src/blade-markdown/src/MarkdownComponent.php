<?php

namespace MaliBoot\BladeMarkdown;

use Hyperf\Context\ApplicationContext;
use Hyperf\ViewEngine\Component\Component;

class MarkdownComponent extends Component
{
    public function __construct(
        protected ?array $options = [],
        protected ?bool $highlightCode = null,
        protected ?string $theme = null,
        protected ?bool $anchors = null,
    ) {
    }

    public function toHtml(string $markdown): string
    {
        $container = ApplicationContext::getContainer();
        $markdownRenderer = $container->get(\Hyperf\Config\config('view_blade_markdown.renderer_class'));

        return $markdownRenderer->toHtml($markdown);
    }

    public function render(): string
    {
        return '<div {{ $attributes }}>{!! $toHtml($slot) !!}</div>';
    }
}
