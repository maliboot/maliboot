<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

use MaliBoot\Utils\Traits\ArrayAccessTrait;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * @deprecated ...
 */
abstract class AbstractValueObject implements ValueObjectInterface, \ArrayAccess
{
    use ArrayAccessTrait;
    use StructureObjectTrait;
}
