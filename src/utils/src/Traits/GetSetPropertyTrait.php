<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Traits;

use Hyperf\Di\ReflectionManager;
use MaliBoot\Cola\Domain\EntityInterface;
use MaliBoot\Cola\Domain\ValueObjectInterface;
use MaliBoot\Cola\Infra\DataObjectInterface;
use MaliBoot\Dto\AbstractDTO;
use MaliBoot\FieldCollector\FieldCollector;
use MaliBoot\Utils\Collection;

trait GetSetPropertyTrait
{
    protected function getClassPropertyValue(string $propertyName): mixed
    {
        if ($this->isReflectMode()) {
            $reflectProperty = $this->setClassPropertyAccessible($propertyName);

            if (! $reflectProperty->isInitialized($this)) {
                return null;
            }

            return $reflectProperty->getValue($this);
        }

        return $this->{$propertyName};
    }

    protected function setClassPropertyValue(string $propertyName, string $propertyType, $propertyValue): static
    {
        $value = $this->convertClassPropertyType($propertyName, $propertyValue, $propertyType);
        if ($this->isReflectMode()) {
            $reflectProperty = $this->setClassPropertyAccessible($propertyName);
            $reflectProperty->setValue($this, $value);
        } else {
            $this->{$propertyName} = $value;
        }

        return $this;
    }

    protected function convertClassPropertyType(string $propertyName, $originalValue, string $targetPropertyType): mixed
    {
        if (is_object($targetPropertyType) || class_exists($targetPropertyType)) {
            return $this->convertClassPropertyTypeForObject($propertyName, $originalValue, $targetPropertyType);
        }
        if ($this->isArray($targetPropertyType)) {
            return $this->convertClassPropertyTypeForArray($propertyName, $originalValue, $targetPropertyType);
        }

        return $this->convertClassPropertyTypeForBaseType($propertyName, $originalValue, $targetPropertyType);
    }

    private function isReflectMode(): bool
    {
        return true;
    }

    private function setClassPropertyAccessible(string $propertyName): \ReflectionProperty
    {
        $reflectProperty = ReflectionManager::reflectClass(static::class)
            ->getProperty($propertyName);
        $reflectProperty->setAccessible(true);
        return $reflectProperty;
    }

    private function isStructureObject(string $targetPropertyType): bool
    {
        return (is_object($targetPropertyType) || class_exists($targetPropertyType)) && (
            is_subclass_of($targetPropertyType, AbstractDTO::class)
            || is_subclass_of($targetPropertyType, EntityInterface::class)
            || is_subclass_of($targetPropertyType, ValueObjectInterface::class)
            || is_subclass_of($targetPropertyType, DataObjectInterface::class)
        );
    }

    private function isCollection(string $targetPropertyType): bool
    {
        return (is_object($targetPropertyType) || class_exists($targetPropertyType)) && is_subclass_of($targetPropertyType, \Hyperf\Collection\Collection::class);
    }

    private function isArray(string $targetPropertyType): bool
    {
        return $targetPropertyType === 'array';
    }

    private function isEqualType(string $targetPropertyType, $originalValue): bool
    {
        return (is_object($targetPropertyType) || class_exists($targetPropertyType))
            && is_object($originalValue)
            && $targetPropertyType === get_class($originalValue);
    }

    private function convertClassPropertyTypeForObject(string $propertyName, $originalValue, string $targetPropertyType): mixed
    {
        // 类型相同
        if ($this->isEqualType($targetPropertyType, $originalValue)) {
            return $originalValue;
        }

        if ($this->isStructureObject($targetPropertyType)) {
            // 类型为结构体
            return call_user_func([$targetPropertyType, 'of'], is_array($originalValue) ? $originalValue : $originalValue->toArray());
        }

        if ($this->isCollection($targetPropertyType)) {
            return $this->convertClassPropertyTypeForArrayOrCollect($propertyName, $originalValue, $targetPropertyType, 'collection');
        }

        return new $targetPropertyType();
    }

    private function convertClassPropertyTypeForArray(string $propertyName, $originalValue, string $targetPropertyType): mixed
    {
        return $this->convertClassPropertyTypeForArrayOrCollection($propertyName, $originalValue, $targetPropertyType, 'array');
    }

    private function convertClassPropertyTypeForArrayOrCollection(string $propertyName, $originalValue, string $targetPropertyType, string $type): mixed
    {
        if ($type === 'array') {
            $newValue = [];
        } else {
            $newValue = new Collection();
        }

        if (! is_array($originalValue) && ! $originalValue instanceof \Hyperf\Collection\Collection) {
            return $newValue;
        }

        // 类型为集合或数组
        $fieldAnnotation = FieldCollector::getField(get_called_class(), $propertyName)['annotation'];
        $refPropertyType = $fieldAnnotation->ref;

        if (empty($refPropertyType)) {
            throw new \RuntimeException(sprintf('未定义类字段(%s::%s)的 Field 注解的 ref 属性.', get_called_class(), $propertyName));
        }

        if (in_array($refPropertyType, ['int', 'string', 'float', 'bool', 'boolean'])) {
            foreach ($originalValue as $item) {
                $newValue = $this->addItemToArrayOrCollection($newValue, $this->convertClassPropertyTypeForBaseType($propertyName, $item, $refPropertyType));
            }

            return $newValue;
        }

        foreach ($originalValue as $item) {
            $newValue = $this->addItemToArrayOrCollection($newValue, call_user_func([$refPropertyType, 'of'], is_array($item) ? $item : $item->toArray()));
        }

        return $newValue;
    }

    private function addItemToArrayOrCollection(array|Collection $items, $value): array|Collection
    {
        if (is_array($items)) {
            $items[] = $value;
        } else {
            $items->push($value);
        }

        return $items;
    }

    private function convertClassPropertyTypeForBaseType(string $propertyName, $originalValue, string $targetPropertyType): mixed
    {
        return match ($targetPropertyType) {
            'int', 'Int', 'INT', 'integer' => (int) $originalValue,
            'string', 'String', 'STRING' => (string) $originalValue,
            'bool', 'boolean', 'Bool', 'Boolean', 'BOOL', 'BOOLEAN' => (bool) $originalValue,
            'float', 'Float', 'FLOAT' => (float) $originalValue,
            default => $originalValue,
        };
    }
}
