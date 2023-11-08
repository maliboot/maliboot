<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

use MaliBoot\Utils\Contract\Arrayable;

/**
 * @deprecated ...
 */
interface EntityInterface extends Arrayable
{
    public function getId();
}
