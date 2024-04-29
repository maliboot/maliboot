<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Cola\Domain\CommandRepositoryInterface;

class AbstractCommonDBRepository extends AbstractDBRepository implements QueryDBRepositoryInterface, RepositoryCriteriaInterface, CommandRepositoryInterface
{
    use AbstractCommandDBRepositoryTrait;
    use AbstractQueryDBRepositoryTrait;
}
