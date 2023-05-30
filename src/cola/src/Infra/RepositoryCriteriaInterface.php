<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Utils\Collection;

interface RepositoryCriteriaInterface
{
    /**
     * 向最后添加用于筛选查询的条件.
     *
     * @param mixed $criteria
     * @return $this
     */
    public function pushCriteria($criteria);

    /**
     * 弹出一个条件.
     *
     * @param mixed $criteria
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * 获取条件集合.
     *
     * @return Collection
     */
    public function getCriteria();

    /**
     * 根据条件获取多条数据.
     *
     * @return mixed
     */
    public function listByCriteria(CriteriaInterface $criteria);

    /**
     * 跳过条件.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * 重置所有条件.
     *
     * @return $this
     */
    public function resetCriteria();
}
