<?php

declare(strict_types=1);

namespace MaliBoot\FieldCollector;

use Hyperf\Di\MetadataCollector;

class FieldCollector extends MetadataCollector
{
    protected static array $container = [];

    public static function setFields(string $class, array $value)
    {
        static::$container[$class] = $value;
    }

    public static function addField(string $class, array $value)
    {
        static::$container[$class][] = $value;
    }

    public static function getFields(string $class): array
    {
        if (! isset(static::$container[$class])) {
            return [];
        }

        return static::$container[$class];
    }

    public static function hasField(string $class, string $field): bool
    {
        if (! isset(static::$container[$class])) {
            return false;
        }

        return isset(static::$container[$class][$field]);
    }

    public static function getField(string $class, string $field): array
    {
        if (! isset(static::$container[$class])) {
            return [];
        }

        if (! isset(static::$container[$class][$field])) {
            return [];
        }

        return static::$container[$class][$field];
    }
}
