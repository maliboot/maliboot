<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface HasManyAnnotationInterface
{
    public function hasManyRelated(): string;

    public function hasManyForeignKey(): ?string;

    public function hasManyLocalKey(): ?string;
}
