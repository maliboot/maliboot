<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\CastAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Cast implements CastAnnotationInterface
{
    /**
     * 属性类型转换，见<a href="https://hyperf.wiki/3.1/#/zh-cn/db/mutators?id=%e5%b1%9e%e6%80%a7%e7%b1%bb%e5%9e%8b%e8%bd%ac%e6%8d%a2">文档</a>.
     * @param null|string $value 数据类型映射
     */
    public function __construct(
        public ?string $value = null,
    ) {}

    public function getCast(): ?string
    {
        return $this->value;
    }
}
