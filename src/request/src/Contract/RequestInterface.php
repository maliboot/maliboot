<?php

declare(strict_types=1);

namespace MaliBoot\Request\Contract;

use MaliBoot\Dto\UserContext;

interface RequestInterface extends \Hyperf\HttpServer\Contract\RequestInterface
{
    public function getUser(): ?UserContext;
}
