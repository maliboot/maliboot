<?php

declare(strict_types=1);

namespace MaliBoot\Event;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\EventDispatcher;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $listeners = $container->get(ListenerProviderInterface::class);
        $stdoutLogger = null;
        $config = $container->get(ConfigInterface::class);
        if ($config->get('debug.hyperf', false)) {
            $stdoutLogger = $container->get(StdoutLoggerInterface::class);
        }
        return new EventDispatcher($listeners, $stdoutLogger);
    }
}
