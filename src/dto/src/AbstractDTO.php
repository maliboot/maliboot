<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Utils\Traits\ArrayAccessTrait;
use MaliBoot\Utils\Traits\StructureObjectTrait;

/**
 * 数据传输对象，包括命令、查询和响应，命令和查询CQRS概念.
 */
abstract class AbstractDTO implements \ArrayAccess
{
    use ArrayAccessTrait;
    use StructureObjectTrait;

    private ?Context $context = null;

    public function getUser(): ?UserContext
    {
        if (is_null($this->context)) {
            return null;
        }

        return $this->context->getUser();
    }

    /**
     * alias of getUser.
     */
    public function user(): ?UserContext
    {
        return $this->getUser();
    }

    public function setUser(?UserContext $user): static
    {
        if (is_null($this->context)) {
            $this->context = new Context();
        }

        $this->context->setUser($user);

        return $this;
    }
}
