<?php

declare(strict_types=1);

namespace MaliBoot\Contract\Auth;

interface Authenticatable
{
    /**
     * 获取用户的唯一标识符的名称.
     */
    public function getAuthIdentifierName(): string;

    /**
     * 获取用户的唯一标识符.
     */
    public function getAuthIdentifier(): string|int;
}
