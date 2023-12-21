<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\ViewObject;

#[ViewObject(name: 'bool')]
class BoolVO
{
    private bool $result;
}
