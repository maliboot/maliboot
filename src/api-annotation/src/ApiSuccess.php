<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ApiSuccess extends AbstractAnnotation
{
    /**
     * @param string $name 参数名称
     * @param string $type 参数类型
     * @param string $default 参数默认值
     * @param string $description 参数描述
     * @param string $example 参数示例
     * @param string $ref 参数引用
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $default = '',
        public string $description = '',
        public string $example = '',
        public string $ref = '',
        public array $options = []
    ) {
    }
}
