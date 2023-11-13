<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Contract;

use MaliBoot\Lombok\Contract\ArrayObjectAnnotationInterface;
use MaliBoot\Lombok\Contract\ToCollectionAnnotationInterface;

interface StructureObjectAnnotationInterface extends ArrayObjectAnnotationInterface, ToCollectionAnnotationInterface, IsPropertyInitAnnotationInterface, MagicToStringAnnotationInterface
{
}
