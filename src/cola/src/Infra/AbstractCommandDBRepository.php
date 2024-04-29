<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Cola\Domain\CommandRepositoryInterface;

abstract class AbstractCommandDBRepository extends AbstractDBRepository implements CommandRepositoryInterface
{
    use AbstractCommandDBRepositoryTrait;
}
