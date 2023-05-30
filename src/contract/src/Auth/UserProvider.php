<?php

declare(strict_types=1);

namespace MaliBoot\Contract\Auth;

interface UserProvider
{
    /**
     * 通过用户的唯一标识符检索用户.
     *
     * @param mixed $identifier
     */
    public function retrieveById($identifier): ?Authenticatable;

    /**
     * 通过给定的凭据检索用户.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable;

    /**
     * 根据给定的凭据验证用户.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool;
}
