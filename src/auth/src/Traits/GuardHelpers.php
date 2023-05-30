<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Traits;

use MaliBoot\Auth\Exception\AuthenticationException;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\UserProvider;

/**
 * 这些方法通常适用于所有 guard 类.
 */
trait GuardHelpers
{
    /**
     * 当前已验证的用户.
     */
    protected ?Authenticatable $user;

    /**
     * 用户提供都实现.
     */
    protected ?UserProvider $provider;

    /**
     * 确定当前用户是否经过身份验证。如果没有，则抛出异常.
     *
     * @throws AuthenticationException
     */
    public function authenticate(): Authenticatable
    {
        if (! is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException();
    }

    /**
     * 确定guard是否具有用户实例.
     */
    public function hasUser(): bool
    {
        return ! is_null($this->user);
    }

    /**
     * 确定当前用户是否经过身份验证
     */
    public function check(): bool
    {
        return ! is_null($this->user());
    }

    /**
     * 确定当前用户是否为访客.
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * 获取当前已验证用户的ID.
     */
    public function id(): int|string|null
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }

        return null;
    }

    /**
     * 设置当前用户.
     *
     * @return $this
     */
    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * 忘记当前用户.
     *
     * @return $this
     */
    public function forgetUser(): static
    {
        $this->user = null;

        return $this;
    }

    /**
     * 获取当前使用的用户提供者.
     */
    public function getProvider(): ?UserProvider
    {
        return $this->provider;
    }

    /**
     * 设置实例使用的用户提供者.
     */
    public function setProvider(UserProvider $provider): void
    {
        $this->provider = $provider;
    }
}
