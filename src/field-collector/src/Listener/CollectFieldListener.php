<?php

declare(strict_types=1);

namespace MaliBoot\FieldCollector\Listener;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\ReflectionManager;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use MaliBoot\Cola\Annotation\AggregateRoot;
use MaliBoot\Cola\Annotation\Column;
use MaliBoot\Cola\Annotation\DataObject;
use MaliBoot\Cola\Annotation\Entity;
use MaliBoot\Cola\Annotation\Field as DomainField;
use MaliBoot\Cola\Annotation\ValueObject;
use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\Field as DTOField;
use MaliBoot\Dto\Annotation\ViewObject;
use MaliBoot\FieldCollector\FieldCollector;

/**
 * @deprecated ...
 */
class CollectFieldListener implements ListenerInterface
{
    protected array $fieldClasses = [
        DataTransferObject::class,
        ViewObject::class,
        ValueObject::class,
        Entity::class,
        AggregateRoot::class,
        DataObject::class,
    ];

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BootApplication::class,
            //            BeforeWorkerStart::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event): void
    {
        foreach ($this->fieldClasses as $className) {
            $this->collectField($className);
        }
    }

    protected function collectField(string $annotationClassName): void
    {
        $classes = AnnotationCollector::getClassesByAnnotation($annotationClassName);
        foreach ($classes as $className => $annotation) {
            if ($this->isDTO($annotationClassName)) {
                $this->collectDTOField($className);
            } elseif ($this->isDomain($annotationClassName)) {
                $this->collectDomainField($className);
            } elseif ($this->isDO($annotationClassName)) {
                $this->collectDOField($className);
            }
        }
    }

    protected function isDTO(string $className): bool
    {
        return in_array($className, [DataTransferObject::class, ViewObject::class]);
    }

    protected function collectDTOField(string $className): void
    {
        $properties = AnnotationCollector::getPropertiesByAnnotation(DTOField::class);
        $this->setValue($className, $properties);
    }

    protected function setValue(string $className, array $properties): void
    {
        $fields = [];
        foreach ($properties as $property) {
            if ($property['class'] !== $className) {
                continue;
            }

            $propertyName = $property['property'];
            $reflectionProperty = ReflectionManager::reflectProperty($className, $propertyName);
            $type = $reflectionProperty->getType();
            if ($type instanceof \ReflectionUnionType) {
                $types = $type->getTypes();
                $typeName = $types[0]->getName();
            } else {
                $typeName = $type->getName();
            }

            $fields[$propertyName] = [
                'name' => $propertyName,
                'type' => $typeName,
                'annotation' => $property['annotation'],
            ];
        }

        FieldCollector::setFields($className, $fields);
    }

    protected function isDomain(string $className): bool
    {
        return in_array($className, [Entity::class, ValueObject::class, AggregateRoot::class]);
    }

    protected function collectDomainField(string $className): void
    {
        $properties = AnnotationCollector::getPropertiesByAnnotation(DomainField::class);
        $this->setValue($className, $properties);
    }

    protected function isDO(string $className): bool
    {
        return in_array($className, [DataObject::class]);
    }

    protected function collectDOField(string $className): void
    {
        $properties = AnnotationCollector::getPropertiesByAnnotation(Column::class);
        $this->setValue($className, $properties);
    }
}
