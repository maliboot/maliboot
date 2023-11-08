<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Domain;

interface ToEntityInterface
{
    /**
     * @return string 获取对应实体类名称
     */
    public function getEntityFQN(): string;
}