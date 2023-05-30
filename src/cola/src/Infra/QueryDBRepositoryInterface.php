<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Dto\AbstractPageQuery;
use MaliBoot\Dto\PageVO;
use MaliBoot\Utils\Collection;

/**
 * 查询存储专用接口.
 */
interface QueryDBRepositoryInterface extends QueryRepositoryInterface
{
    /**
     * 根据 ID 获取单条数据.
     *
     * @param int $id ID
     * @param array<string> $columns 查询字段
     *
     * @return null|DataObjectInterface
     */
    public function getById(int $id, array $columns = ['*']);

    /**
     * 根据字段获取单条数据.
     *
     * @param string $field 字段名称
     * @param mixed $value 字段值
     * @param array<string> $columns 查询字段
     *
     * @return null|DataObjectInterface
     */
    public function getByField(string $field, $value = null, array $columns = ['*']);

    /**
     * 获取 ID 获取多条数据.
     *
     * @param array<int> $ids
     * @param array<string> $columns 查询字段
     */
    public function listByIds(array $ids, array $columns = ['*']): Collection;

    /**
     * 根据条件获取分页数据.
     *
     * @return PageVO 分页数据
     */
    public function listByPage(AbstractPageQuery $pageQuery): PageVO;

    /**
     * 获取总数.
     *
     * @param string $columns
     */
    public function count(array $where = [], $columns = '*'): int;

    /**
     * 查询指定字段的选择数据数组.
     *
     * @return array|Collection
     */
    public function lists(string $column, ?string $key = null);

    /**
     * 查询指定字段的选择数据数组.
     *
     * @return array|Collection
     */
    public function pluck(string $column, ?string $key = null);

    /**
     * 查询所有数据.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * All 别名.
     */
    public function get(array $columns = ['*']): Collection;

    /**
     * Retrieve first data of repository.
     *
     * @return mixed
     */
    public function first(array $columns = ['*']);

    /**
     * 获取指定条数的数据.
     */
    public function limit(int $limit, array $columns = ['*']): Collection;

    /**
     * 根据多个字段获取多条数据.
     */
    public function listByWhere(array $where, array $columns = ['*']): Collection;

    /**
     * 根据一个字段中的多个值获取多条数据.
     */
    public function listByWhereIn(string $field, array $values, array $columns = ['*']): Collection;

    /**
     * 根据排除一个字段中的多个值获取多条数据.
     */
    public function listByWhereNotIn(string $field, array $values, array $columns = ['*']): Collection;

    /**
     * 根据一个字段中的范围值获取多条数据.
     */
    public function listByWhereBetween(string $field, array $values, array $columns = ['*']): Collection;

    /**
     * 设置查询的 “orderBy” 值
     *
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy(string $column, $direction = 'asc');

    /**
     * 设置查询的 “limit” 值
     *
     * @return $this
     */
    public function take(int $limit);
}
