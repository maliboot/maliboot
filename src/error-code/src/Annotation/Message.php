<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class Message extends AbstractAnnotation
{
}
