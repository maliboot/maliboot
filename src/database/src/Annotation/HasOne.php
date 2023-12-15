<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\HasOneAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HasOne implements HasOneAnnotationInterface
{
    /**
     * 模型关联-1对一.
     * @param null|string $foreignKey 当前表在关联表的外键名称
     * @param null|string $localKey 当前表的主键字段名称
     */
    public function __construct(
        public ?string $foreignKey = null,
        public ?string $localKey = null,
    ) {}

    public function hasOneForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function hasOneLocalKey(): ?string
    {
        return $this->localKey;
    }
}
