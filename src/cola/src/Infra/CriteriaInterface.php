<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

interface CriteriaInterface
{
    /**
     * 在查询存储库中应用条件.
     *
     * @param mixed $do
     * @return mixed
     */
    public function apply($do, QueryRepositoryInterface $repository);
}
