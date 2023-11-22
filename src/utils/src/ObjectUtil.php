<?php

namespace MaliBoot\Utils;

use MaliBoot\Cola\Annotation\AggregateRoot;
use MaliBoot\Cola\Annotation\Database;
use MaliBoot\Cola\Annotation\Entity;
use MaliBoot\Cola\Annotation\ValueObject;
use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\ViewObject;
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
        if (! (is_string($firstItem) || is_a($firstItem))) {
            return '';
        }

        return $firstItem;
    }

    public static function isDTO(string|object $clazz): bool
    {
        return self::hasAttribute($clazz, self::$DTOAttributes);
    }

    public static function isVO(string|object $clazz): bool
    {
        return self::hasAttribute($clazz, self::$VOAttributes);
    }

    public static function isDomainObject(string|object $clazz): bool
    {
        return self::hasAttribute($clazz, self::$domainObjectAttributes);
    }

    public static function isDataObject(string|object $clazz): bool
    {
        return self::hasAttribute($clazz, self::$dataObjectAttributes);
    }

    public static function hasAttribute(string|object $clazz, array $filterAttributes): bool
    {
        if (is_string($clazz) && ! class_exists($clazz)) {
            return false;
        }
        $class = new ReflectionClass($clazz);
        foreach ($class->getAttributes() as $attribute) {
            if (in_array($attribute->getName(), $filterAttributes)) {
                return true;
            }
        }

        return false;
    }
}