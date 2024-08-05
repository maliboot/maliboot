<?php

namespace MaliBoot\Utils;

use MaliBoot\Cola\Annotation\AggregateRoot;
use MaliBoot\Cola\Annotation\Database;
use MaliBoot\Cola\Annotation\Entity;
use MaliBoot\Cola\Annotation\ValueObject;
use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\ViewObject;
use MaliBoot\Lombok\Contract\OfAnnotationInterface;
use MaliBoot\Lombok\Contract\ToArrayAnnotationInterface;
use ReflectionAttribute;
use ReflectionClass;

class ObjectUtil
{
    protected static array $DTOAttributes = [DataTransferObject::class];

    protected static array $VOAttributes = [ViewObject::class];

    protected static array $domainObjectAttributes = [Entity::class, AggregateRoot::class, ValueObject::class];

    protected static array $dataObjectAttributes = [Database::class];

    public static function isCollectionAttribute(Collection $collection, array $filterAttributes): bool
    {
        $firstItem = self::getCollectionFirstClazz($collection);
        if ($firstItem === '') {
            return false;
        }
        return self::hasAttribute($firstItem, $filterAttributes);
    }

    public static function isCollectionDTO(Collection $collection): bool
    {
        return self::isCollectionAttribute($collection, self::$DTOAttributes);
    }

    public static function isCollectionVO(Collection $collection): bool
    {
        return self::isCollectionAttribute($collection, self::$VOAttributes);
    }

    public static function isCollectionDomainObject(Collection $collection): bool
    {
        return self::isCollectionAttribute($collection, self::$domainObjectAttributes);
    }

    public static function isCollectionDataObject(Collection $collection): bool
    {
        return self::isCollectionAttribute($collection, self::$dataObjectAttributes);
    }

    protected static function getCollectionFirstClazz(Collection $collection): string|object
    {
        if ($collection->isEmpty()) {
            return '';
        }

        $firstItem = $collection->first();
        if (! (is_string($firstItem) || is_object($firstItem))) {
            return '';
        }

        return $firstItem;
    }

    public static function isClazzVar(mixed $var) : bool
    {
        return (is_string($var) && class_exists($var)) || is_object($var);
    }

    public static function isDTO(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, self::$DTOAttributes);
    }

    public static function isOf(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, [OfAnnotationInterface::class]);
    }

    public static function isToArray(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, [ToarrayAnnotationInterface::class]);
    }

    public static function isVO(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, self::$VOAttributes);
    }

    public static function isDomainObject(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, self::$domainObjectAttributes);
    }

    public static function isDataObject(mixed $clazz): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        return self::hasAttribute($clazz, self::$dataObjectAttributes);
    }

    public static function hasAttribute(mixed $clazz, array $filterAttributes): bool
    {
        if (! self::isClazzVar($clazz)) {
            return false;
        }
        $class = new ReflectionClass($clazz);
        foreach ($filterAttributes as $filterAttribute) {
            if (! empty($class->getAttributes($filterAttribute, ReflectionAttribute::IS_INSTANCEOF))) {
                return true;
            }
        }

        return false;
    }
}