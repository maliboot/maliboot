<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\BelongsToManyAnnotationInterface;
use MaliBoot\Lombok\Contract\FieldArrayTypeAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BelongsToMany implements BelongsToManyAnnotationInterface, FieldArrayTypeAnnotationInterface
{
    /**
     * 模型关联-多对多.
     * @param string $related 关联模型（类）名称
     * @param null|string $table 中间表名称，默认为当前模型与关联模型以"_"拼接而成
     * @param null|string $foreignPivotKey 当前表在中间表的外键名称
     * @param null|string $relatedPivotKey 关联表在中间表的外键名称
     * @param null|string $parentKey 当前表的主键字段名称
     * @param null|string $relatedKey 关联表的主键字段名称
     * @param null|string $relation 无关联名称时，将默认使用字段名称
     */
    public function __construct(
        public string $related,
        public ?string $table = null,
        public ?string $foreignPivotKey = null,
        public ?string $relatedPivotKey = null,
        public ?string $parentKey = null,
        public ?string $relatedKey = null,
        public ?string $relation = null,
    ) {}

    public function belongsToManyTable(): ?string
    {
        return $this->table;
    }

    public function belongsToManyForeignPivotKey(): ?string
    {
        return $this->foreignPivotKey;
    }

    public function belongsToManyRelatedPivotKey(): ?string
    {
        return $this->relatedPivotKey;
    }

    public function belongsToManyParentKey(): ?string
    {
        return $this->parentKey;
    }

    public function belongsToManyRelatedKey(): ?string
    {
        return $this->relatedKey;
    }

    public function belongsToManyRelation(): ?string
    {
        return $this->relation;
    }

    public function belongsToManyRelated(): string
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
