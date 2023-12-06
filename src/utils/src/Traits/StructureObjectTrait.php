<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Traits;

use Hyperf\Di\ReflectionManager;
use Hyperf\Utils\Codec\Json;
use MaliBoot\Utils\Contract\Arrayable;

/**
 * @deprecated ...
 */
trait StructureObjectTrait
{
    use GetterAndSetterTrait;
    use SetPropertiesTrait;

    public function __toString(): string
    {
        $result = $this->toArray();
        if (empty($result)) {
            $result = new \stdClass();
        }

        return Json::encode($result);
    }

    public static function of(array $args): static
    {
        $structureObject = new static();
        $structureObject->setProperties($args);
        return $structureObject;
    }

    public function toArray(): array
    {
        return $this->parseArray($this->getFields());
    }

    public function getFields(): array
    {
        $data = [];
        $properties = $this->getProperties();
        foreach ($properties as $property) {
            $name = $property['name'];

            if (! $this->isPropertyInitialized($name)) {
                continue;
            }

            $data[$name] = $this->getClassPropertyValue($name);
        }
        return $data;
    }

    protected function parseArray(array $array): array
    {
        if (empty($array)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            if ($this->hasToArray($value)) {
                $array[$key] = $value->toArray();
                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }

    protected function hasToArray($data): bool
    {
        return $data instanceof \Hyperf\Contract\Arrayable
            || $data instanceof Arrayable
            || (is_object($data) && method_exists($data, 'toArray'));
    }

    protected function isPropertyInitialized(string $property): bool
    {
        $reflectProperty = ReflectionManager::reflectClass(static::class)
            ->getProperty($property);
        $reflectProperty->setAccessible(true);
        return $reflectProperty->isInitialized($this);
    }
}
