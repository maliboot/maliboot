<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use Hyperf\Database\Model\Builder;
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
        /** @var AbstractModelDelegate $model */
        foreach ($models as &$model) {
            if (! $model instanceof AbstractModelDelegate) {
                continue;
            }
            $doClassName = $model->delegatedSource();
            foreach ($model->concerns as $concernField => $doClass) {
                // avoid circular reference
                if (! empty($model->withConcerns) && in_array($concernField, $model->withConcerns)) {
                    $model->load($concernField);
                }
            }

            $model = (new $doClassName())->setMyDelegate($model)->ofData($model->toArray());
        }
        return new Collection($models);
    }

    public function newModelBuilder($query): Builder
    {
        return new class($query) extends Builder {
            public function __construct($query)
            {
                parent::__construct($query);
            }

            /**
             * Create a new instance of the model being queried.
             *
             * @param array $attributes
             * @return \Hyperf\Database\Model\Model|static
             */
            public function newModelInstance($attributes = [])
            {
                $model = parent::newModelInstance($attributes);
                if (! empty($this->model->withConcerns)) {
                    $model->withConcerns = $this->model->withConcerns;
                }
                return $model;
            }
        };
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $modelIns = parent::newInstance($attributes, $exists);
        if (! empty($this->withConcerns)) {
            $modelIns->withConcerns = $this->withConcerns;
        }
        return $modelIns;
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

    public function joiningTableSegment(): string
    {
        return str_replace('_do', '', parent::joiningTableSegment());
    }

    public function createGetId($values, $sequence = null)
    {
        $values = $this->columnsFormat($values, true);
        return $this->newQuery()->insertGetId($values, $sequence);
    }

    protected function newRelatedInstance($class)
    {
        $relatedModel = parent::newRelatedInstance($class);
        $relatedModel->getMyDelegate()->withConcerns = $this->withConcerns;
        return $relatedModel;
    }
}
