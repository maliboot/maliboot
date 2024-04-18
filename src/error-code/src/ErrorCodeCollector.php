<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode;

use Hyperf\Di\MetadataCollector;
use Hyperf\Stringable\Str;

class ErrorCodeCollector extends MetadataCollector
{
    protected static array $container = [];

    public static function setValue($code, array $value)
    {
        static::$container[$code] = $value;
    }

    public static function getMessage(int|string $code): string
    {
        if (static::hasCode($code)) {
            // TODO 临时处理方式，后续需要优化
            $message = static::$container[$code]['message'];
            if (function_exists('trans') && Str::startsWith($message, 'error.')) {
                $message = trans($message);
            }
            return $message;
        }

        return '';
    }

    public static function getStatusCode(int|string $code): int
    {
        if (static::hasCode($code) && ! empty(static::$container[$code]['statusCode'])) {
            return static::$container[$code]['statusCode'];
        }

        return 200;
    }

    public static function hasCode(int|string $code): bool
    {
        return array_key_exists($code, static::$container);
    }

    public static function list(): array
    {
        return static::$container;
    }
}
