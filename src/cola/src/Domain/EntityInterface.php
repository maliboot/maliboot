<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

use MaliBoot\Utils\Contract\Arrayable;

interface EntityInterface extends Arrayable
{
    public function getId();
}
