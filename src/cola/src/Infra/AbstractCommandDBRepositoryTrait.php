<?php
declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use MaliBoot\Cola\Exception\RepositoryException;

/**
 * @property Builder|mixed $do ...
 */
trait AbstractCommandDBRepositoryTrait
{
    public function save(object|array $entity, string $primaryKey = 'id'): bool|int
    {
        if (is_object($entity)) {
            $callMethod = 'get' . ucfirst($primaryKey);
            if (! method_exists($entity, $callMethod) || $entity->{$callMethod}() === null) {
                return $this->create($entity);
            }

            return $this->update($entity);
        }

        if (empty($entity[$primaryKey])) {
            return $this->create($entity);
        }
        return $this->update($entity);
    }

    public function create(object|array $entity): int
    {
        is_object($entity) && $entity = $entity->toArray();
        if (empty($entity)) {
            return 0;
        }
        return $this->getDO()->createGetId($entity);
    }

    public function update(object|array $entity): bool
    {
        $do = $this->getDO();
        $values = $do->columnsFormat(is_object($entity) ? $entity->toArray() : $entity, true);
        $primaryKey = $do->getKeyName();

        if (! isset($values[$primaryKey])) {
            throw new RepositoryException(500, sprintf(
                '%s::update(object $entity)缺失主键[%s]值：%s',
                $this::class,
                $primaryKey,
                json_encode($values)
            ));
        }

        $primaryValue = $values[$primaryKey];
        unset($values[$primaryValue]);
        if (is_object($entity)) {
            $primaryValue = call_user_func([$entity, 'get' . ucfirst($primaryKey)]);
        }
        return (bool) $do->where($primaryKey, $primaryValue)->update($values);
    }

    public function delete(array|Collection|int|string $ids): int
    {
        return $this->getDO()->destroy($ids);
    }

    public function find(int|string $id, ?string $entityFQN = null): ?object
    {
        $do = $this->getDO()->find($id);
        if (! empty($do)) {
            return $do->toEntity($entityFQN);
        }

        return null;
    }

    public function findBy(string $field, $value, ?string $entityFQN = null): ?object
    {
        $do = $this->getDO()->where($field, $value)->first();
        if (! empty($do)) {
            return $do->toEntity($entityFQN);
        }

        return null;
    }

    public function firstBy(array $where, ?string $entityFQN = null): ?object
    {
        $result = $this->applyConditions($where)->first();
        $this->reset();
        return $result?->toEntity($entityFQN);
    }

    public function allBy(array $where, ?string $entityFQN = null): ?Collection
    {
        $result = $this->applyConditions($where)->get()?->map(function ($item) use ($entityFQN) {
            /* @var AbstractModelDelegate $item ... */
            return $item->toEntity($entityFQN);
        });
        $this->reset();
        return $result;
    }

    /**
     * 批量添加.
     * @param object[] $entities ...
     * @return bool ...
     */
    public function insert(array $entities): bool
    {
        $values = array_reduce($entities, function ($carry, $item) {
            if (is_object($item) && method_exists($item, 'toArray') && ! empty($itemData = $item->toArray())) {
                $carry[] = $itemData;
            }
            return $carry;
        }, []);
        if (empty($values)) {
            return false;
        }
        $do = $this->getDO();
        $values = array_map(function ($item) use ($do) {
            return $do->columnsFormat($item, true);
        }, $values);
        return $do->insert($values);
    }

    public function batchUpdate(array $entities): int
    {
        return $this->batchUpdateByIds(array_reduce($entities, function ($carry, $item) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                $itemVal = $item->toArray();
            } else {
                $itemVal = $item;
            }
            $carry[] = $this->getDO()->columnsFormat($itemVal, true);
            return $carry;
        }, []));
    }
}