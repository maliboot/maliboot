<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\HasManyAnnotationInterface;
use MaliBoot\Lombok\Contract\FieldArrayTypeAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HasMany implements HasManyAnnotationInterface, FieldArrayTypeAnnotationInterface
{
    /**
     * 模型关联-1对多.
     * @param string $related 关联模型（类）名称
     * @param null|string $foreignKey 当前表在关联表的外键名称
     * @param null|string $localKey 当前表的主键字段名称
     */
    public function __construct(
        public string $related,
        public ?string $foreignKey = null,
        public ?string $localKey = null,
    ) {}

    public function hasManyForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function hasManyLocalKey(): ?string
    {
        return $this->localKey;
    }

    public function hasManyRelated(): string
    {
        return $this->related;
    }

    public function arrayKeyType(): ?string
    {
        return null;
    }

    public function arrayValueType(): ?string
    {
        return $this->related;
    }
}
