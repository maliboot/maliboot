<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
abstract class ApiResponse extends AbstractAnnotation
{
    public function __construct(public string $value, public string $type, public int $statusCode = 200)
    {
    }
}
