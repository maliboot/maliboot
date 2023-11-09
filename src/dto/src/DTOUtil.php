<?php

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\ViewObject;
use ReflectionClass;

class DTOUtil
{
    public static function isDTO(string $clazz): bool
    {
        return self::hasAttribute($clazz, [DataTransferObject::class]);
    }

    public static function isVO(string $clazz): bool
    {
        return self::hasAttribute($clazz, [ViewObject::class]);
    }

    public static function hasAttribute(string $clazz, array $filterAttributes): bool
    {
        $class = new ReflectionClass($clazz);
        foreach ($class->getAttributes() as $attribute) {
            if (in_array($attribute->getName(), $filterAttributes)) {
                return true;
            }
        }

        return false;
    }
}