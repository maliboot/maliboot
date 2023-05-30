<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\HttpServer\Annotation\Mapping;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiMapping extends Mapping
{
    /**
     * @param null|string $path 路径
     * @param array $methods 方法
     * @param string $name 名称
     * @param string $summary 简述
     * @param string $description 描述
     * @param array $options 选项
     */
    public function __construct(
        public ?string $path = null,
        public array $methods = [],
        public string $name = '',
        public string $summary = '',
        public string $description = '',
        public array $options = [],
    ) {
    }
}
