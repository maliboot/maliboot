<?php

namespace MaliBoot\BladeMarkdown;

use Spatie\LaravelMarkdown\MarkdownRenderer as LaravelMarkdownRender;
use MaliBoot\BladeMarkdown\Contract\MarkdownRenderInterface;

class MarkdownRender extends LaravelMarkdownRender implements MarkdownRenderInterface
{
    public function toHtml(string $markdown): string
    {
        return $this->convertMarkdownToHtml($markdown);
    }

}