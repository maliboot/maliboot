<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiPageResponse extends AbstractAnnotation
{
    public function __construct(public string $value, public string $type = 'page', public int $statusCode = 200)
    {
    }
}
