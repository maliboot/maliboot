<?php

declare(strict_types=1);

namespace MaliBoot\Framework\Bootstrap;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\Framework\Event\OtherWorkerStart;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Server as SwooleServer;

class WorkerStartCallback
{
    public function __construct(protected EventDispatcherInterface $dispatcher, protected StdoutLoggerInterface $logger)
    {
    }

    /**
     * Handle Swoole onWorkerStart event.
     */
    public function onWorkerStart(SwooleServer $server, int $workerId)
    {
        $this->dispatcher->dispatch(new BeforeWorkerStart($server, $workerId));

        if ($workerId === 0) {
            $this->dispatcher->dispatch(new MainWorkerStart($server, $workerId));
        } else {
            $this->dispatcher->dispatch(new OtherWorkerStart($server, $workerId));
        }

        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $hyperfDebug = $config->get('debug.hyperf', false);

        if ($server->taskworker) {
            $hyperfDebug && $this->logger->info("TaskWorker#{$workerId} started.");
        } else {
            $hyperfDebug && $this->logger->info("Worker#{$workerId} started.");
        }

        $this->dispatcher->dispatch(new AfterWorkerStart($server, $workerId));
        CoordinatorManager::until(Constants::WORKER_START)->resume();
    }
}
