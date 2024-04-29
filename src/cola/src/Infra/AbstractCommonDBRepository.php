<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Cola\Domain\CommandRepositoryInterface;
use MaliBoot\Dto\PageVO;
use MaliBoot\Utils\Collection;

class AbstractCommonDBRepository implements QueryDBRepositoryInterface, CommandRepositoryInterface
{
    protected AbstractCommandDBRepository $commandDelegate;

    protected AbstractQueryDBRepository $queryDelegate;

    public function __construct()
    {
        $this->commandDelegate = new class() extends AbstractCommandDBRepository {
            public function resetDO(): object
            {
                return $this->makeDO();
            }
        };
        $this->queryDelegate = new class() extends AbstractQueryDBRepository {
            public function resetDO(): object
            {
                return $this->makeDO();
            }
        };

        $do = $this->do();
        $this->commandDelegate->changeDO($do);
        $this->queryDelegate->changeDO($do);
    }

    protected function do(): string
    {
        $repo = get_class($this);
        $do = str_replace(['Repository', 'Qry', 'Cmd', 'Repo'], ['DataObject', '', '', 'DO'], $repo);
        if (! class_exists($do)) {
            return '';
        }

        return $do;
    }

    public function getById(int $id, array $columns = ['*'])
    {
        return $this->queryDelegate->getById($id, $columns);
    }

    public function getByField(string $field, $value = null, array $columns = ['*'])
    {
        return $this->queryDelegate->getByField($field, $value, $columns);
    }

    public function listByIds(array $ids, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->listByIds($ids, $columns);
    }

    public function listByPage(mixed $pageQuery): PageVO
    {
        return $this->queryDelegate->listByPage($pageQuery);
    }

    public function count(array $where = [], $columns = '*'): int
    {
        return $this->queryDelegate->count($where, $columns);
    }

    public function lists(string $column, ?string $key = null)
    {
        return $this->queryDelegate->lists($column, $key);
    }

    public function pluck(string $column, ?string $key = null)
    {
        return $this->queryDelegate->pluck($column, $key);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->queryDelegate->all($columns);
    }

    public function get(array $columns = ['*']): Collection
    {
        return $this->queryDelegate->get($columns);
    }

    public function first(array $columns = ['*'])
    {
        return $this->queryDelegate->first($columns);
    }

    public function limit(int $limit, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->limit($limit, $columns);
    }

    public function listByWhere(array $where, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->listByWhere($where, $columns);
    }

    public function listByWhereIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->listByWhereIn($field, $values, $columns);
    }

    public function listByWhereNotIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->listByWhereNotIn($field, $values, $columns);
    }

    public function listByWhereBetween(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->queryDelegate->listByWhereBetween($field, $values, $columns);
    }

    public function orderBy(string $column, $direction = 'asc')
    {
        return $this->queryDelegate->listByWhereBetween($column, $direction);
    }

    public function take(int $limit)
    {
        return $this->queryDelegate->take($limit);
    }

    public function updateById(array $values): bool
    {
        return $this->queryDelegate->updateById($values);
    }

    public function create(object|array $entity): int
    {
        return $this->commandDelegate->create($entity);
    }

    public function update(object|array $entity): bool
    {
        return $this->commandDelegate->update($entity);
    }

    public function save(object|array $entity): bool|int
    {
        return $this->commandDelegate->save($entity);
    }

    public function delete(int|string $id): int
    {
        return $this->commandDelegate->delete($id);
    }

    public function find(int|string $id): ?object
    {
        return $this->commandDelegate->find($id);
    }

    public function findBy(string $field, mixed $value): ?object
    {
        return $this->commandDelegate->findBy($field, $value);
    }

    public function firstBy(array $where, ?string $entityFQN = null): ?object
    {
        return $this->commandDelegate->firstBy($where, $entityFQN);
    }

    public function insert(array $entities): bool
    {
        return $this->commandDelegate->insert($entities);
    }

    public function allBy(array $where, ?string $entityFQN = null): ?\Hyperf\Collection\Collection
    {
        return $this->commandDelegate->allBy($where, $entityFQN);
    }

    public function batchUpdate(array $entities): int
    {
        return $this->commandDelegate->batchUpdate($entities);
    }

    public function batchUpdateByIds(array $values): int
    {
        return $this->queryDelegate->batchUpdateByIds($values);
    }
}
