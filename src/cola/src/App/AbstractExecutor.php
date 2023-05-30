<?php

declare(strict_types=1);

namespace MaliBoot\Cola\App;

use Hyperf\Contract\ContainerInterface;

abstract class AbstractExecutor implements ExecutorInterface
{
    public function __construct(public ContainerInterface $container)
    {
    }
}
