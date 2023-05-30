<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field extends AbstractAnnotation
{
    /**
     * @param string $in 参数位置
     * @param string $name 参数名称
     * @param string $type 参数类型
     * @param string $default 参数默认值
     * @param string $desc 参数描述
     * @param string $example 参数示例
     * @param string $ref 参数引用
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $in = '',
        public string $default = '',
        public string $desc = '',
        public string $example = '',
        public string $ref = '',
    ) {
    }

    public function collectProperty(string $className, ?string $target): void
    {
        parent::collectProperty($className, $target);
    }
}
