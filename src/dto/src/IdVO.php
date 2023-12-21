<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\ViewObject;

#[ViewObject(name: 'id')]
class IdVO
{
    private int|string $id;
}
