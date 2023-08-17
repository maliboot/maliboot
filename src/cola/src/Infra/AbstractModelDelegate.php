<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @method string delegatedSource() 关联的DataObject类名称
 */
abstract class AbstractModelDelegate extends Model
{
    public function newCollection(array $models = []): Collection
    {
        foreach ($models as &$model) {
            if (! $model instanceof AbstractModelDelegate) {
                continue;
            }
            $doClassName = $model->delegatedSource();
            $model = (new $doClassName())->ofData($model->toArray())->setMyDelegate($model);
        }
        return new Collection($models);
    }

    public function fill(array $attributes)
    {
        $attributes = $this->columnsFormat($attributes, true);
        return parent::fill($attributes);
    }

    /**
     * 格式化表字段.
     *
     * @param bool $isTransSnake 是否转snake
     */
    public function columnsFormat(array $value, bool $isTransSnake = false): array
    {
        $formatValue = [];
        foreach ($value as $field => $fieldValue) {
            // 转snake
            $isTransSnake && $field = Str::snake($field);
            // 过滤
            if (! in_array($field, $this->fillable)) {
                continue;
            }
            $formatValue[$field] = $fieldValue;
        }
        return $formatValue;
    }

    public function createGetId($values, $sequence = null)
    {
        $values = $this->columnsFormat($values, true);
        return $this->newQuery()->insertGetId($values, $sequence);
    }

    public function fillPropertiesFromAttributes(): void
    {
//        $this->setProperties($this->getAttributes());
    }
}
