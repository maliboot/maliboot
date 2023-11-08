<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\ViewObject;

/**
 * @method bool getResult() ...
 * @method self setResult(bool $result) ...
 */
#[ViewObject(name: 'bool')]
class BoolVO
{
    private bool $result;
}
