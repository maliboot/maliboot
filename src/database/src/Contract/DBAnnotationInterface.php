<?php

declare(strict_types=1);

namespace MaliBoot\Database\Contract;

use MaliBoot\Dto\Contract\StructureObjectAnnotationInterface;

interface DBAnnotationInterface extends StructureObjectAnnotationInterface
{
    public function getTable(): ?string;

    public function softDeletes(): bool;

    public function getConnection(): string;

    public function getCastsAttributes(): string;
}
