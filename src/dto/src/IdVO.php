<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\ViewObject;

/**
 * @method int|string getId() ...
 * @method self setId(int|string $id) ...
 */
#[ViewObject(name: 'id')]
class IdVO
{
    private int|string $id;
}
