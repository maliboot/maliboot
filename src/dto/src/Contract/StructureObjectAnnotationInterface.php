<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Contract;

use MaliBoot\Lombok\Contract\GetterAnnotationInterface;
use MaliBoot\Lombok\Contract\OfAnnotationInterface;
use MaliBoot\Lombok\Contract\SetterAnnotationInterface;
use MaliBoot\Lombok\Contract\ToArrayAnnotationInterface;
use MaliBoot\Lombok\Contract\ToCollectionAnnotationInterface;

interface StructureObjectAnnotationInterface extends GetterAnnotationInterface, SetterAnnotationInterface, OfAnnotationInterface, ToArrayAnnotationInterface, ToCollectionAnnotationInterface, IsPropertyInitAnnotationInterface, MagicToStringAnnotationInterface
{
}
