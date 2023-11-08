<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Cola\Client\Constants\FieldRelationType;

/**
 * @deprecated ...
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field extends AbstractAnnotation
{
    public function __construct(
        public string $desc = '',
        public string $value = '',
        public string $name = '',
        public bool $required = true,
        public bool $related = false,
        public string $relationType = FieldRelationType::ASSOCIATION,
        public bool $relationRequired = false,
        public string $position = '',
        public string $ref = '',
    ) {
    }
}
