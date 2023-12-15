<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface HasOneAnnotationInterface
{
    public function hasOneForeignKey(): ?string;

    public function hasOneLocalKey(): ?string;
}
