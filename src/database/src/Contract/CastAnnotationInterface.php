<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

interface CastAnnotationInterface
{
    public function getCast(): ?string;
}
