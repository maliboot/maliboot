<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Traits;

use MaliBoot\FieldCollector\FieldCollector;

trait GetterAndSetterTrait
{
    use GetSetPropertyTrait;

    /**
     * @param string $method
     * @param array $args
     *
     * @return null|mixed|void
     */
    public function __call($method, $args)
    {
        $propertyName = lcfirst(substr($method, 3));
        $getterSetterMethod = substr($method, 0, 3);
        if ($getterSetterMethod === 'set') {
            $propertyType = FieldCollector::getField(static::class, $propertyName);
            $this->setClassPropertyValue($propertyName, $propertyType['type'], $args[0]);
            return $this;
        }

        if ($getterSetterMethod === 'get') {
            return $this->getClassPropertyValue($propertyName);
        }

        throw new \BadMethodCallException(sprintf('%s method not found', $method));
    }
}
