<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @deprecated ...
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column extends AbstractAnnotation
{
    public function __construct(
        public string $name = '',
        public string $desc = '',
        public string $type = '',
        public bool $hidden = false,
        public string $alias = ''
    ) {
    }
}
