<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\AbstractViewObject as VO;
use MaliBoot\Dto\Annotation\Field;
use MaliBoot\Dto\Annotation\ViewObject;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * @method int|string getId() ...
 * @method self setId(int|string $id) ...
 */
#[ViewObject(name: 'id')]
class IdVO extends VO
{
    use StructureObjectTrait;

    #[Field(name: 'id', type: 'string')]
    private int|string $id;
}
