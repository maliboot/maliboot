<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Closure;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Stringable\Str;
use MaliBoot\Cola\Exception\RepositoryException;
use MaliBoot\ErrorCode\Constants\ServerErrorCode;

abstract class AbstractDBRepository
{
    /**
     * @var Builder ...
     */
    protected mixed $do;

    protected ?string $doFQN = null;

    public function reset(): static
    {
        $this->resetDO();
        return $this;
    }

    public function resetDO(): object
    {
        $this->doFQN = null;
        return $this->makeDO();
    }

    /**
     * @param string $doFQN ...
     * @return Builder ...
     */
    public function changeDO(string $doFQN): mixed
    {
        $this->doFQN = $doFQN;
        return $this->makeDO();
    }

    /**
     * @return Builder ...
     */
    public function getDO(): mixed
    {
        return $this->makeDO();
    }

    /**
     * @return Builder ...
     */
    protected function makeDO(): mixed
    {
        return $this->do = \Hyperf\Support\make($this->doFQN ?: $this->do());
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

    /**
     * 将给定的 where 条件应用于 DO.
     * @param array $where Sample<code>
     *
     * $where = ['id' => 1];
     * $where = ['id', '>',  1];
     * $where = ['id', '<',  1];
     * $where = ['id', '!=',  1];
     * $where = ['id', 'IN',  [1, 2]];
     * $where = ['id', 'NOT IN',  [1, 2]];
     * $where = ['name', 'LIKE',  '%foo%'];
     * $where = ['name', 'NOT LIKE',  '%foo%'];
     * $where = ['`price` > IF(`state` = "TX", ?, 100)', 'RAW',  [200]]; // todo ...
     * $where = [
     *      ['id' => 1],
     *      ['name', 'LIKE',  '%foo%'],
     * ]
     * </code>
     *
     * @return Builder ...
     */
    protected function applyConditions(array $where): mixed
    {
        // 组织结构
        if (isset($where[0], $where[1]) && ! is_array($where[0]) && ! is_array($where[1])) {
            $where = [$where];
        }
        $do = empty($this->do) ? $this->getDO() : $this->do;
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                if (! isset($value[1]) && is_string($valueField = array_key_first($value))) {
                    $value = [$valueField, QueryConnector::OPERATOR_EQ->value, $value[$valueField]];
                }
                if (! isset($value[2])) {
                    if (strtoupper((string) $value[1]) === 'RAW') {
                        $value[2] = [];
                    } else {
                        $value = [$value[0], QueryConnector::OPERATOR_EQ->value, $value[1]];
                    }
                }
                [$field, $condition, $val] = $value;
                if ($condition instanceof QueryConnector) {
                    $condition = $condition->value;
                }
                if (! (is_string($condition) && strtoupper($condition) === 'RAW')) {
                    $field = Str::snake($field);
                }
                // smooth input
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                // split to get operator, syntax: "DATE >", "DATE =", "DAY <"
                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else {
                    $operator = null;
                }
                switch (QueryConnector::from(strtoupper($condition))) {
                    case QueryConnector::IN:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereIn($field, $val);
                        break;
                    case QueryConnector::NOT_IN:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereNotIn($field, $val);
                        break;
                    case QueryConnector::DATE:
                        if (! $operator) {
                            $operator = QueryConnector::OPERATOR_EQ->value;
                        }
                        $do = $do->whereDate($field, $operator, $val);
                        break;
                    case QueryConnector::DAY:
                        if (! $operator) {
                            $operator = QueryConnector::OPERATOR_EQ->value;
                        }
                        $do = $do->whereDay($field, $operator, $val);
                        break;
                    case QueryConnector::MONTH:
                        if (! $operator) {
                            $operator = QueryConnector::OPERATOR_EQ->value;
                        }
                        $do = $do->whereMonth($field, $operator, $val);
                        break;
                    case QueryConnector::YEAR:
                        if (! $operator) {
                            $operator = QueryConnector::OPERATOR_EQ->value;
                        }
                        $do = $do->whereYear($field, $operator, $val);
                        break;
                    case QueryConnector::EXISTS:
                        if (! $val instanceof Closure) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} must be closure function");
                        }
                        $do = $do->whereExists($val);
                        break;
                    case QueryConnector::HAS:
                        if (! $val instanceof Closure) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} must be closure function");
                        }
                        $do = $do->whereHas($field, $val);
                        break;
                    case QueryConnector::HAS_MORPH:
                        if (! $val instanceof Closure) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} must be closure function");
                        }
                        $do = $do->whereHasMorph($field, $val);
                        break;
                    case QueryConnector::DOESNT_HAVE:
                        if (! $val instanceof Closure) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} must be closure function");
                        }
                        $do = $do->whereDoesntHave($field, $val);
                        break;
                    case QueryConnector::DOESNT_HAVE_MORPH:
                        if (! $val instanceof Closure) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} must be closure function");
                        }
                        $do = $do->whereDoesntHaveMorph($field, $val);
                        break;
                    case QueryConnector::BETWEEN:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereBetween($field, $val);
                        break;
                    case QueryConnector::BETWEEN_COLUMNS:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereBetweenColumns($field, $val);
                        break;
                    case QueryConnector::NOT_BETWEEN:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereNotBetween($field, $val);
                        break;
                    case QueryConnector::NOT_BETWEEN_COLUMNS:
                        if (! is_array($val)) {
                            throw new RepositoryException(ServerErrorCode::WHERE_INVALID_PARAMS, "Input {$val} mus be an array");
                        }
                        $do = $do->whereNotBetweenColumns($field, $val);
                        break;
                    case QueryConnector::RAW:
                        $do = $do->whereRaw($field, $val);
                        break;
                    default:
                        $do = $do->where($field, $condition, $val);
                }
            } else {
                $field = Str::snake((string) $field);
                $do = $do->where($field, QueryConnector::OPERATOR_EQ->value, $value);
            }
        }
        return $do;
    }

    /**
     * 批量修改 - case...then...根据主键.
     * @param array $values 修改数据(必须包含ID)
     * @return int 影响条数
     */
    public function batchUpdateByIds(array $values): int
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
