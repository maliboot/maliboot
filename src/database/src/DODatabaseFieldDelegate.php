<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use Hyperf\Database\Model\Collection;
use MaliBoot\Lombok\Contract\GetterSetterDelegateInterface;

class DODatabaseFieldDelegate implements GetterSetterDelegateInterface
{
    public static function get(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        return $value;
    }

    public static function set(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        $classInstance->getMyDelegate()->{$name} = $value;
        return $value;
    }
}
