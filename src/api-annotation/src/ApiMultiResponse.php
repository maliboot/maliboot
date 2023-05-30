<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiMultiResponse extends AbstractAnnotation
{
    public function __construct(public string $value, public string $type = 'multi', public int $statusCode = 200)
    {
    }
}
