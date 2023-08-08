<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Lombok\contract\GetterAnnotationInterface;
use MaliBoot\Lombok\contract\SetterAnnotationInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DomainObject extends AbstractAnnotation implements GetterAnnotationInterface, SetterAnnotationInterface
{
    public function __construct(public string $domain = '', public bool $isAggregateRoot = false, public string $name = '', public string $desc = '')
    {
    }
}
