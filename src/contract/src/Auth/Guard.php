<?php

declare(strict_types=1);

namespace MaliBoot\Contract\Auth;

interface Guard
{
    /**
     * 确定当前用户是否经过身份验证
     */
    public function check(): bool;

    /**
     * 确定当前用户是否为访客.
     */
    public function guest(): bool;

    /**
     * 获取当前已验证的用户.
     */
    public function user(): ?Authenticatable;

    /**
     * 获取当前已验证用户的ID.
     */
    public function id(): int|string|null;

    /**
     * 判断是否具有用户实例.
     */
    public function hasUser(): bool;

    /**
     * 设置当前用户.
     */
    public function setUser(Authenticatable $user): static;

    /**
     * 将用户登录到应用.
     */
    public function login(Authenticatable $user): mixed;

    /**
     * 将给定的用户ID登录到应用中.
     *
     * @return mixed;
     */
    public function loginUsingId(int|string $id): mixed;

    /**
     * 将用户注销应用.
     */
    public function logout(): bool;
}
