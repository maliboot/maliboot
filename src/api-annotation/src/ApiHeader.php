<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_METHOD)]
class ApiHeader extends ApiParam
{
    /**
     * @param string $in 参数位置
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
        public string $in = 'header',
        public string $default = '',
        public string $description = '',
        public string $example = '',
        public string $ref = '',
        public array $options = []
    ) {
    }
}
