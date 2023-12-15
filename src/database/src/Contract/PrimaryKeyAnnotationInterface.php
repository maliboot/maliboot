<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface PrimaryKeyAnnotationInterface
{
    public function getPrimaryKeyName(): ?string;

    public function getPrimaryKeyType(): ?string;
}
