<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Hyperf\Contract\PaginatorInterface;
use Hyperf\Database\Model\Builder;
use MaliBoot\Cola\Exception\RepositoryException;
use MaliBoot\Dto\PageVO;
use MaliBoot\Utils\Collection;
use MaliBoot\Utils\ObjectUtil;

abstract class AbstractQueryDBRepository extends AbstractDBRepository implements QueryRepositoryInterface, RepositoryCriteriaInterface
{
    protected ?Collection $criteria = null;

    protected bool $skipCriteria = false;

    protected ?\Closure $scopeQuery = null;

    /**
     * 触发对 DO 的静态方法调用.
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * 触发对 DO 的方法调用.
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->applyCriteria();
        $this->applyScope();

        return call_user_func_array([$this->do, $method], $arguments);
    }

    /**
     * 获取条件集合.
     *
     * @return Collection
     */
    public function getCriteria()
    {
        if (is_null($this->criteria)) {
            $this->resetCriteria();
        }

        return $this->criteria;
    }

    /**
     * 重置所有条件.
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * 根据 ID 获取单条数据.
     *
     * @param int $id ID
     * @param array<string> $columns 查询字段
     *
     * @return null|DataObjectInterface
     */
    public function getById(int $id, array $columns = ['*'])
    {
        return $this->getByField('id', $id, $columns);
    }

    /**
     * 根据单个字段获取单条数据.
     *
     * @param string $field 字段名称
     * @param mixed $value 字段值
     * @param array<string> $columns 查询字段
     *
     * @return null|DataObjectInterface
     */
    public function getByField(string $field, $value = null, array $columns = ['*'])
    {
        $this->applyCriteria()->applyScope();

        $do = $this->do->where($field, '=', $value)->first($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * Retrieve first data of repository.
     *
     * @return mixed
     */
    public function first(array $columns = ['*'])
    {
        $this->applyCriteria()->applyScope();

        $results = $this->do->first($columns);

        $this->reset();

        return $this->parserResult($results);
    }

    /**
     * 重置 DO、查谒范围和条件.
     *
     * @return $this
     */
    public function reset(): static
    {
        $this->resetDO();
        $this->resetScope();
        $this->resetCriteria();
        return $this;
    }

    /**
     * 重置查询范围.
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * 对结果数据进行处理.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parserResult($result)
    {
        if ($result instanceof \Hyperf\Collection\Collection) {
            if ($result->isEmpty()) {
                return new Collection();
            }

            $result = $result->map(function ($value, $key) {
                return $this->parserResult($value);
            });

            $result = new Collection($result->all());
        } elseif ($result instanceof PaginatorInterface) {
            //            if ($result->isEmpty()) {
            //                return new PageVO();
            //            }

            $items = [];
            foreach ($result->items() as $item) {
                $items[] = $this->parserResult($item);
            }

            $newResult = new PageVO($items);
            $newResult->setPageIndex($result->currentPage());
            $newResult->setTotalCount($result->total());
            $newResult->setPageSize($result->perPage());
            return $newResult;
        }

        return $result;
    }

    /**
     * 查询所有数据.
     */
    public function all(array $columns = ['*']): Collection
    {
        $this->applyCriteria()->applyScope();

        if (ObjectUtil::isDataObject($this->do)) {
            $results = $this->do->get($columns);
        } else {
            $results = $this->do->all($columns);
        }

        $this->reset();

        return $this->parserResult($results);
    }

    /**
     * All 别名.
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->all($columns);
    }

    /**
     * 根据多个字段获取单条数据.
     *
     * @return null|DataObjectInterface
     */
    public function getByWhere(array $where, array $columns = ['*'])
    {
        $this->do = $this->applyCriteria()->applyScope()->applyConditions($where);
        $do = $this->do->first($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 获取 ID 获取多条数据.
     *
     * @param array<int> $ids
     * @param array<string> $columns 查询字段
     */
    public function listByIds(array $ids, array $columns = ['*']): Collection
    {
        $this->applyCriteria()->applyScope();

        $do = $this->do->whereIn('id', $ids)->get($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 根据条件获取分页数据.
     *
     * @return PageVO 分页数据
     */
    public function listByPage(mixed $pageQuery): PageVO
    {
        $this->applyCriteria()->applyScope();

        $where = $pageQuery->getFilters();
        if ($where) {
            $this->do = $this->applyConditions($where);
        }

        if (! empty($orderBy = $pageQuery->getOrderBy())) {
            if (is_array($orderBy) && count($orderBy) === 2 && isset($orderBy[0]) && ! is_array($orderBy[0])) {
                $this->do = $this->do->orderBy($orderBy[0], $orderBy[1]);
            } elseif (is_array($orderBy) && isset($orderBy[0]) && is_array($orderBy[0])) {
                foreach ($orderBy as $item) {
                    $this->do = $this->do->orderBy($item[0], $item[1]);
                }
            } elseif (is_string($orderBy) && strpos($orderBy, ' ') !== false) {
                [$column, $direction] = explode(' ', $orderBy);
                $this->do = $this->do->orderBy($column, $direction);
            } elseif (is_string($orderBy)) {
                $this->do = $this->do->orderBy($orderBy);
            }
        }

        if (! empty($groupBy = $pageQuery->getGroupBy())) {
            $this->do = $this->do->groupBy($groupBy);
        }

        // 分页参数
        $perPage = ! empty($pageQuery->getPageSize()) ? $pageQuery->getPageSize() : 1;
        $page = ! empty($pageQuery->getPageIndex()) ? $pageQuery->getPageIndex() : null;

        // 分页
        $do = $this->do->paginate($perPage, $pageQuery->getColumns(), 'page', $page);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 设置查询的 “orderBy” 值
     *
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy(string $column, $direction = 'asc')
    {
        $this->do = $this->do->orderBy($column, $direction);

        return $this;
    }

    /**
     * 获取总数.
     *
     * @param string $columns
     */
    public function count(array $where = [], $columns = '*'): int
    {
        $this->applyCriteria()->applyScope();

        if ($where) {
            $this->do = $this->applyConditions($where);
        }

        $result = $this->do->count($columns);

        $this->reset();

        return $result;
    }

    /**
     * 查询范围.
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * 查询指定字段的选择数据数组.
     *
     * @return array|Collection
     */
    public function lists(string $column, ?string $key = null)
    {
        $this->applyCriteria()->applyScope();

        return $this->do->lists($column, $key);
    }

    /**
     * 查询指定字段的选择数据数组.
     *
     * @return array|Collection
     */
    public function pluck(string $column, ?string $key = null)
    {
        $this->applyCriteria()->applyScope();

        $results = $this->do->pluck($column, $key);

        return $this->parserResult($results);
    }

    /**
     * 获取指定条数的数据.
     */
    public function limit(int $limit, array $columns = ['*']): Collection
    {
        return $this->take($limit)->all($columns);
    }

    /**
     * 设置查询的 “limit” 值
     *
     * @return $this
     */
    public function take(int $limit)
    {
        $this->do = $this->do->limit($limit);

        return $this;
    }

    /**
     * 根据多个字段获取多条数据.
     */
    public function listByWhere(array $where, array $columns = ['*']): Collection
    {
        $this->do = $this->applyCriteria()->applyScope()->applyConditions($where);
        $do = $this->do->get($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 根据一个字段中的多个值获取多条数据.
     */
    public function listByWhereIn(string $field, array $values, array $columns = ['*']): Collection
    {
        $this->applyCriteria()->applyScope();
        $do = $this->do->whereIn($field, $values)->get($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 根据排除一个字段中的多个值获取多条数据.
     */
    public function listByWhereNotIn(string $field, array $values, array $columns = ['*']): Collection
    {
        $this->applyCriteria()->applyScope();
        $do = $this->do->whereNotIn($field, $values)->get($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 根据一个字段中的范围值获取多条数据.
     */
    public function listByWhereBetween(string $field, array $values, array $columns = ['*']): Collection
    {
        $this->applyCriteria()->applyScope();
        $do = $this->do->whereBetween($field, $values)->get($columns);
        $this->reset();

        return $this->parserResult($do);
    }

    /**
     * 向最后添加用于筛选查询的条件.
     *
     * @param mixed $criteria
     * @return $this
     * @throws RepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria();
        }
        if (! $criteria instanceof CriteriaInterface) {
            throw new RepositoryException(500, 'Class ' . get_class($criteria) . ' must be an instance of MaliBoot\\Cola\\Infra\\CriteriaInterface');
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * 弹出一个条件.
     *
     * @param mixed $criteria
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * 根据条件获取多条数据.
     */
    public function listByCriteria(CriteriaInterface $criteria, array $columns = ['*']): Collection
    {
        $this->do = $criteria->apply($this->do, $this);
        $results = $this->do->get($columns);
        $this->reset();

        return $this->parserResult($results);
    }

    /**
     * 跳过条件.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * 在当前查询中应用条件.
     *
     * @return $this
     */
    protected function applyCriteria()
    {
        $this->makeDO();
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->do = $c->apply($this->do, $this);
                }
            }
        }

        return $this;
    }

    /**
     * 在当前查询中应用范围.
     *
     * @return $this
     */
    protected function applyScope(): static
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->do = $callback($this->do);
        }

        return $this;
    }
}
