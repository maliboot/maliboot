<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra\Ast\Generator;

use MaliBoot\Dto\Contract\StructureObjectAnnotationInterface;

interface DatabaseAnnotationInterface extends StructureObjectAnnotationInterface, ToEntityAnnotationInterface, OfEntityAnnotationInterface
{
    public function getTable(): ?string;

    public function useSoftDeletes(): bool;

    public function getConnection(): string;

    public function getCastsAttributes(): string;
}
