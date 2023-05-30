<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Provider;

use MaliBoot\Contract\Auth\UserProvider;

abstract class AbstractUserProvider implements UserProvider
{
    public function __construct(protected array $config, protected string $name)
    {
    }
}
