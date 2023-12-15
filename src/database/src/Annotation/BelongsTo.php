<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\BelongsToAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BelongsTo implements BelongsToAnnotationInterface
{
    /**
     * 模型关联-多对一.
     * @param null|string $foreignKey 当前表在关联表的外键名称
     * @param null|string $ownerKey 当前表的主键字段名称
     * @param null|string $relation 无关联名称时，此处将默认使用字段名称
     */
    public function __construct(
        public ?string $foreignKey = null,
        public ?string $ownerKey = null,
        public ?string $relation = null,
    ) {}

    public function belongsToForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function belongsToOwnerKey(): ?string
    {
        return $this->ownerKey;
    }

    public function belongsToRelation(): ?string
    {
        return $this->relation;
    }
}
