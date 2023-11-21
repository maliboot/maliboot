<?php

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\ViewObject;
use ReflectionClass;
use Hyperf\Collection\Collection;

class DTOUtil
{
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
        $firstItem = self::getCollectionFirstClazz($collection);
        if ($firstItem === '') {
            return false;
        }
        return DTOUtil::isDTO($firstItem);
    }

    public static function isCollectionVO(Collection $collection): bool
    {
        $firstItem = self::getCollectionFirstClazz($collection);
        if ($firstItem === '') {
            return false;
        }
        return DTOUtil::isVO($firstItem);
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
        return self::hasAttribute($clazz, [DataTransferObject::class]);
    }

    public static function isVO(string|object $clazz): bool
    {
        return self::hasAttribute($clazz, [ViewObject::class]);
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