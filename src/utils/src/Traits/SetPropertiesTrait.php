<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Traits;

use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use MaliBoot\FieldCollector\FieldCollector;

/**
 * @deprecated ...
 */
trait SetPropertiesTrait
{
    use GetSetPropertyTrait;

    /**
     * @param int $instanceType 0-读 1-创建 2-更新
     */
    public function setProperties(array $args): void
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $parameters = [];
        foreach ($args as $key => $arg) {
            $parameters[Str::camel($key)] = $arg;
        }

        $properties = $this->getProperties();

        foreach ($properties as $property) {
            if (! array_key_exists($property['name'], $parameters)) {
                continue;
            }

            $this->setClassPropertyValue(
                $property['name'],
                $property['type'],
                Arr::get($parameters, $property['name'])
            );
        }
    }

    /**
     * 获取属性.
     */
    protected function getProperties(): array
    {
        return FieldCollector::getFields(static::class);
    }
}
