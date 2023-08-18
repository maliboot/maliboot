<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Contract\StructureObjectAnnotationInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class AggregateRoot extends AbstractAnnotation implements StructureObjectAnnotationInterface
{
    public function __construct(public string $domain = '', public string $name = '', public string $desc = '', public ?string $getterSetterDelegate = null)
    {
    }

    public function getterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }

    public function setterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }
}
