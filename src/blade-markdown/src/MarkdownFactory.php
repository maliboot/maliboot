<?php

namespace MaliBoot\BladeMarkdown;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class MarkdownFactory
{
    public function __invoke(ContainerInterface $container, array $parameters = [])
    {
        $configIns = $container->get(ConfigInterface::class);
        $config = $configIns->get('view_blade_markdown');

        $options = empty($parameters['options']) ? $config['commonmark_options'] : array_merge($config['commonmark_options'], $parameters['options']);
        return make(MarkdownRender::class, [
            'commonmarkOptions' => $options,
            'highlightCode' => $this->highlightCode ?? $config['code_highlighting']['enabled'],
            'highlightTheme' => $this->theme ?? $config['code_highlighting']['theme'],
            'cacheStoreName' => $config['cache_store'],
            'renderAnchors' => $this->anchors ?? $config['add_anchors_to_headings'],
            'extensions' => $config['extensions'],
            'blockRenderers' => $config['block_renderers'],
            'inlineRenderers' => $config['inline_renderers'],
            'inlineParsers' => $config['inline_parsers'],
        ]);
    }
}