<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

use MaliBoot\Utils\Traits\ArrayAccessTrait;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * 领域实体.
 * @deprecated ...
 */
abstract class AbstractEntity implements EntityInterface, \ArrayAccess
{
    use ArrayAccessTrait;
    use StructureObjectTrait;

    public function getId(): null|int|string
    {
        return $this->__call('getId', []);
    }
}
