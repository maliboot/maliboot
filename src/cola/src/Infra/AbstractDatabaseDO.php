<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use Hyperf\Database\Model\Concerns\CamelCase;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Stringable\Str;
use MaliBoot\Cola\Annotation\Column;
use MaliBoot\Cola\Annotation\DataObject;
use MaliBoot\Cola\Domain\AggregateRootInterface;
use MaliBoot\Cola\Domain\EntityInterface;
use MaliBoot\FieldCollector\FieldCollector;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * @deprecated be instead of \MaliBoot\Cola\Infra\AbstractModelDelegate
 */
abstract class AbstractDatabaseDO extends BaseModel implements DataObjectInterface
{
    use StructureObjectTrait, CamelCase {
        StructureObjectTrait::__call as getterAndSetterCall;
        StructureObjectTrait::toArray insteadof CamelCase;
    }

    protected static bool $initialized = false;

    public function __construct(array $attributes = [])
    {
        $this->initializeProperties();
        parent::__construct($attributes);
        $this->setProperties($attributes);
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return null|mixed|void
     */
    public function __call($method, $args)
    {
        $propertyName = lcfirst(substr($method, 3));
        if (! FieldCollector::hasField(static::class, $propertyName)) {
            return parent::__call($method, $args);
        }

        return $this->getterAndSetterCall($method, $args);
    }

    public static function ofEntity(object $entity): static
    {
        return new static($entity->toArray());
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
        $this->setProperties($this->getAttributes());
    }

    /**
     * @return null|AggregateRootInterface|EntityInterface ...
     */
    public function toEntity(?string $entityFQN = null)
    {
        if ($entityFQN == null) {
            $entityFQN = $this->getEntityFQN();
        }
        if ($entityFQN === null || ! class_exists($entityFQN)) {
            return null;
        }
        return call_user_func([$entityFQN, 'of'], $this->attributesToArray());
    }

    protected function initializeProperties(): void
    {
        //        if (static::$initialized)  {
        //            return;
        //        }

        $dataObject = $this->getDataObjectAnnotation();

        if (! empty($dataObject->table)) {
            $this->setTable($dataObject->table);
        }

        if (! empty($dataObject->connection)) {
            $this->setConnection($dataObject->connection);
        }

        $this->initFillable();
        $this->initCasts();

        static::$initialized = true;
    }

    protected function getDataObjectAnnotation()
    {
        return AnnotationCollector::list()[get_called_class()]['_c'][DataObject::class] ?? null;
    }

    protected function initFillable(): void
    {
        //        if (static::$initialized)  {
        //            return;
        //        }

        $properties = AnnotationCollector::list()[get_called_class()]['_p'];
        $fillable = [];
        foreach ($properties as $property) {
            foreach ($property as $item) {
                if (! $item instanceof Column) {
                    continue;
                }

                $fillable[] = Str::snake($item->name);
            }
        }
        $this->fillable(array_unique($fillable));
    }

    protected function initCasts(): void
    {
        if (static::$initialized) {
            return;
        }
    }

    /**
     * @return null|string 获取对应实体FQN，可重写
     */
    protected function getEntityFQN(): ?string
    {
        $dataObject = get_class($this);
        $dataObjectArr = explode('\\', $dataObject);
        $dataObjectClassName = end($dataObjectArr);
        $dataObjectClassName = rtrim($dataObjectClassName, 'DO');

        return str_replace(['Infra\DataObject', 'DO'], ['Domain\Model\\' . $dataObjectClassName, ''], $dataObject);
    }
}
