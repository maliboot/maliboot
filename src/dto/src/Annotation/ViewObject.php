<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Contract\BaseDTOAnnotationInterface;
use MaliBoot\Dto\Contract\OfDOAnnotationInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class ViewObject extends AbstractAnnotation implements BaseDTOAnnotationInterface, OfDOAnnotationInterface
{
    public function __construct(public string $name = '', public string $desc = '')
    {
    }
}
