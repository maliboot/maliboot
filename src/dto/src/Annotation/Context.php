<?php

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Contract\ContextAnnotationInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class Context extends AbstractAnnotation implements ContextAnnotationInterface
{
}