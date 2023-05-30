<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class Auth extends AbstractAnnotation
{
    public function __construct(public ?string $value = null)
    {
        parent::__construct($value);
    }
}
