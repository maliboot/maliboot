<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

use Hyperf\Collection\Collection;
use MaliBoot\Cola\Exception\RepositoryException;

/**
 * 每个聚合对应一个存储库，主要实现增删改和为聚合服务的简单的查询.
 */
interface CommandRepositoryInterface
{
    /**
     * 单条添加.
     * @param object $entity ...
     * @return int ...
     */
    public function create(object|array $entity): int;

    /**
     * 单条修改.
     * @param object $entity ...
     * @return bool ...
     * @throws RepositoryException ...
     */
    public function update(object|array $entity): bool;

    /**
     * 单条保存.
     * @param object $entity ...
     * @return bool|int ...
     */
    public function save(object|array $entity): bool|int;

    /**
     * 单条删除.
     * @param int|string $id ...
     * @return int ...
     */
    public function delete(int|string $id): int;

    /**
     * 单条查询-根据id.
     * @param int|string $id ...
     * @return null|object ...
     */
    public function find(int|string $id): ?object;

    /**
     * 单条查询-根据单条件.
     * @param string $field ...
     * @param mixed $value ...
     * @return null|object ...
     */
    public function findBy(string $field, mixed $value): ?object;

    /**
     * 单条查询-根据条件.
     * @param array $where Sample<code>
     *
     * $where = ['id', '>',  1];
     * $where = ['id', '<',  1];
     * $where = ['id', '!=',  1];
     * $where = ['id', 'IN',  [1, 2]];
     * $where = ['id', 'NOT IN',  [1, 2]];
     * $where = ['name', 'LIKE',  '%foo%'];
     * $where = ['name', 'NOT LIKE',  '%foo%'];
     * $where = ['`price` > IF(`state` = "TX", ?, 100)', 'RAW',  [200]]; // todo add
     * $where = [
     *      ['name', 'LIKE',  '%foo%'],
     * ]
     * </code>
     * @param null|string $entityFQN ...
     * @return null|object ...
     */
    public function firstBy(array $where, ?string $entityFQN = null): ?object;

    /**
     * 批量添加.
     * @param object[] $entities ...
     * @return bool ...
     */
    public function insert(array $entities): bool;

    /**
     * 多条查询-根据条件.
     * @param array $where Sample<code>
     *
     * $where = ['id', '>',  1];
     * $where = ['id', '<',  1];
     * $where = ['id', '!=',  1];
     * $where = ['id', 'IN',  [1, 2]];
     * $where = ['id', 'NOT IN',  [1, 2]];
     * $where = ['name', 'LIKE',  '%foo%'];
     * $where = ['name', 'NOT LIKE',  '%foo%'];
     * $where = ['`price` > IF(`state` = "TX", ?, 100)', 'RAW',  [200]]; // todo add
     * $where = [
     *      ['name', 'LIKE',  '%foo%'],
     * ]
     * </code>
     * @param null|string $entityFQN 指定转化的实体FQN
     * @return null|Collection<object> 实体集合：Collection[object]
     */
    public function allBy(array $where, ?string $entityFQN = null): ?Collection;

    /**
     * 批量修改-根据主键.
     * @param object[] $entities 实体列表<br/>1、必须包含有主键；<br/>2、字段数量必须保持一致；<br/>3、例子：<code>
     *
     * // other code ...
     * class Foo implement object
     * {
     *    private int $id;
     *    private int $name;
     *    // other code ...
     * }
     * class FooCmdRepo implement CommandRepositoryInterface
     * {
     *    // other code ...
     * }
     *
     * // 这是错误的参数1（缺失主键字段）
     * $values1 = [
     *      (new Foo())->setName('foo'),
     *      (new Foo())->setName('foo2'),
     * ];
     * // 这是错误的参数2（字段数量不一致）
     * $values2 = [
     *      (new Foo())->setId(1),
     *      (new Foo())->setId(2)->setName('foo2'),
     * ];
     * // 这是正确的参数
     * $values = [
     *      (new Foo())->setId(1)->setName('foo'),
     *      (new Foo())->setId(2)->setName('foo2'),
     * ];
     *
     * $affectedRows = (new FooCmdRepo())->batchUpdate($values);
     * print $affectedRows;
     * </code>
     * @return int 受响应条数
     */
    public function batchUpdate(array $entities): int;
}
