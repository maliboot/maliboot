<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Db;
use MaliBoot\Cola\Domain\CommandRepositoryInterface;
use MaliBoot\Cola\Exception\RepositoryException;

abstract class AbstractCommandDBRepository extends AbstractDBRepository implements CommandRepositoryInterface
{
    public function save(object $entity, string $primaryKey = 'id'): bool|int
    {
        $callMethod = 'get' . ucfirst($primaryKey);
        if (! method_exists($entity, $callMethod) || $entity->{$callMethod}() === null) {
            return $this->create($entity);
        }

        return $this->update($entity);
    }

    public function create(object $entity): int
    {
        return $this->getDO()->createGetId($entity->toArray());
    }

    public function update(object $entity): bool
    {
        $do = $this->getDO();
        $values = $do->columnsFormat($entity->toArray(), true);
        $primaryKey = $do->getKeyName();
        if (! isset($values[$primaryKey])) {
            throw new RepositoryException(500, sprintf(
                '%s::update(object $entity)缺失主键[%s]值：%s',
                $this::class,
                $primaryKey,
                json_encode($values)
            ));
        }
        $primaryValue = call_user_func([$entity, 'get' . ucfirst($primaryKey)]);
        return (bool) $do->where('id', $primaryValue)->update($values);
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
            if (method_exists($item, 'toArray') && ! empty($itemData = $item->toArray())) {
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
            if (method_exists($item, 'toArray')) {
                $carry[] = $this->getDO()->columnsFormat($item->toArray(), true);
            }
            return $carry;
        }, []));
    }

    /**
     * 批量修改 - case...then...根据主键.
     * @param array $values 修改数据(必须包含ID)
     * @return int 影响条数
     */
    private function batchUpdateByIds(array $values): int
    {
        if (empty($values)) {
            return 0;
        }

        $do = $this->getDO();

        # ksort
        foreach ($values as &$value) {
            ksort($value);
            $value = $do->columnsFormat($value, true);
        }
        $tablePrefix = Db::connection($do->getConnectionName())->getTablePrefix();
        $table = $do->getTable();
        $primary = $do->getKeyName();
        $sql = $this->compileBatchUpdateByIds($tablePrefix . $table, $values, $primary);

        return Db::update($sql);
    }

    /**
     * Compile batch update Sql.
     * @param string $table ...
     * @param array $values ...
     * @param string $primary ...
     * @return string update sql
     */
    private function compileBatchUpdateByIds(string $table, array $values, string $primary): string
    {
        if (! is_array(reset($values))) {
            $values = [$values];
        }

        // Take the first value as columns
        $columns = array_keys(current($values));

        $setStr = '';
        foreach ($columns as $column) {
            if ($column === $primary) {
                continue;
            }

            $setStr .= " `{$column}` = case `{$primary}` ";
            foreach ($values as $row) {
                $value = $row[$column];
                $rowValue = is_string($value) ? "'{$value}'" : $value;

                $setStr .= " when '{$row[$primary]}' then {$rowValue} ";
            }
            $setStr .= ' end,';
        }
        // Remove the last character
        $setStr = substr($setStr, 0, -1);

        $ids = array_column($values, $primary);
        $idsStr = implode(',', $ids);

        return "update {$table} set {$setStr} where {$primary} in ({$idsStr})";
    }
}
