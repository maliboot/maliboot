<?php

namespace MaliBoot\BladeMarkdown\Contract;

interface MarkdownRenderInterface
{
    public function toHtml(string $markdown): string;
}