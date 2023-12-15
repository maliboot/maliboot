<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use MaliBoot\Lombok\Contract\GetterSetterDelegateInterface;

class DODatabaseFieldDelegate implements GetterSetterDelegateInterface
{
    public static function get(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        $delegateIns = $classInstance->getMyDelegate();
        if ($value === null && isset($delegateIns->concerns[$name])) {
            return $delegateIns->{'with' . $name}();
        }
        return $value;
    }

    public static function set(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        $classInstance->getMyDelegate()->{$name} = $value;
        return $value;
    }
}
