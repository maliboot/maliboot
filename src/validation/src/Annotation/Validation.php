<?php

declare(strict_types=1);

namespace MaliBoot\Validation\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Validation extends AbstractAnnotation
{
    /**
     * @param array|string $rule 验证规则
     * @param array|string $message 验证消息
     */
    public function __construct(
        public string|array $rule,
        public string|array $message = '',
    ) {
    }
}
