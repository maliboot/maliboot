<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiVersion extends AbstractAnnotation
{
    public function __construct(public string $version, public array $options = [])
    {
    }
}
