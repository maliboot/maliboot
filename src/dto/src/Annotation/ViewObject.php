<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ViewObject extends AbstractAnnotation
{
    public function __construct(public string $name = '', public string $desc = '')
    {
    }
}
