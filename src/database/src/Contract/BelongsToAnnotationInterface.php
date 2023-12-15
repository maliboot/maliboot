<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface BelongsToAnnotationInterface
{
    public function belongsToForeignKey(): ?string;

    public function belongsToOwnerKey(): ?string;

    public function belongsToRelation(): ?string;
}
