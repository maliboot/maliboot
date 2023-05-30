<?php

declare(strict_types=1);

namespace MaliBoot\Cola\App;

use Hyperf\Contract\ContainerInterface;

abstract class AbstractService implements ServiceInterface
{
    public function __construct(public ContainerInterface $container)
    {
    }
}
