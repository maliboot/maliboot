<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiRequest extends AbstractAnnotation
{
    public function __construct(public string $value, public array $options = [])
    {
    }
}
