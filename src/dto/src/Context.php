<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

class Context
{
    /**
     * @var array<string, ContextInterface>
     */
    private array $contextMap = [];

    public function getUser(): ?UserContext
    {
        return isset($this->contextMap['user']) && $this->contextMap['user'] instanceof UserContext
            ? $this->contextMap['user']
            : null;
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
        $this->contextMap['user'] = $user;
        return $this;
    }
}
