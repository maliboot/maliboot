<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use Hyperf\Database\Model\Collection;
use MaliBoot\Lombok\Contract\GetterSetterDelegateInterface;

class DODatabaseFieldDelegate implements GetterSetterDelegateInterface
{
    public static function get(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        /** @var AbstractModelDelegate $delegateIns */
        $delegateIns = $classInstance->getMyDelegate();
        if ($value === null && isset($delegateIns->concerns[$name]) && ! empty($delegateIns->withConcerns) && in_array($name, $delegateIns->withConcerns)) {
            /** @var Collection $concernData */
            $concernData = $delegateIns->{$name};
            if (str_contains($type, 'array')) {
                $concernData = $concernData->all();
            } else {
                $concernData = $concernData->isEmpty() ? $value : $concernData->first();
            }
            // 避免重复查询
            $classInstance->{'set' . ucfirst($name)}($concernData);
            return $concernData;
        }
        return $value;
    }

    public static function set(string $name, mixed $value, string $type, object $classInstance): mixed
    {
        $classInstance->getMyDelegate()->{$name} = $value;
        return $value;
    }
}
