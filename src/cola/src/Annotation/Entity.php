<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity extends AbstractAnnotation
{
    public function __construct(public string $domain = '', public string $name = '', public string $desc = '')
    {
    }
}
