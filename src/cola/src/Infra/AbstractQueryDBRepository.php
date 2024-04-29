<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

abstract class AbstractQueryDBRepository extends AbstractDBRepository implements QueryRepositoryInterface, RepositoryCriteriaInterface
{
    use AbstractQueryDBRepositoryTrait;
}
