<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface BelongsToManyAnnotationInterface
{
    public function belongsToManyRelated(): string;

    public function belongsToManyTable(): ?string;

    public function belongsToManyForeignPivotKey(): ?string;

    public function belongsToManyRelatedPivotKey(): ?string;

    public function belongsToManyParentKey(): ?string;

    public function belongsToManyRelatedKey(): ?string;

    /**
     * @return null|string 无关联名称时，此处将默认使用字段名称
     */
    public function belongsToManyRelation(): ?string;
}
