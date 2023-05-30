<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\Field;
use MaliBoot\Dto\Annotation\ViewObject;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * @method bool getResult() ...
 * @method self setResult(bool $result) ...
 */
#[ViewObject(name: 'bool')]
class BoolVO extends AbstractViewObject
{
    use StructureObjectTrait;

    #[Field(name: 'result', type: 'bool')]
    private bool $result;
}
