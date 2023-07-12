<?php

namespace MaliBoot\BladeMarkdown\Listener;

use Hyperf\Command\Event\BeforeHandle;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeServerStart;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Framework\Event\OnStart;
use Hyperf\Logger\Logger;
use Hyperf\Process\Event\BeforeProcessHandle;
use Hyperf\Server\Event\MainCoroutineServerStart;
use Hyperf\ViewEngine\Blade;
use Hyperf\Logger\LoggerFactory;

#[Listener]
class MarkdownListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $logger = \Hyperf\Support\make(LoggerFactory::class)->get('maliboot');
        if (\Hyperf\Config\config('view', null) === null) {
            $logger->warning('blade-markdown is base on config.view');
            return;
        }
        $echoMarkdown = 'echo make(\\' . \Hyperf\Config\config(('view_blade_markdown.renderer_class')) . '::class)->toHtml';
        Blade::directive('markdown', fn ($markdown) => '<?php ' . ($markdown ? "{$echoMarkdown}({$markdown})" : 'ob_start()') . '; ?>');
        Blade::directive('endmarkdown', fn () => "<?php {$echoMarkdown}(ob_get_clean()); ?>");
    }
}