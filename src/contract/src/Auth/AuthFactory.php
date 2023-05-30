<?php

declare(strict_types=1);

namespace MaliBoot\Contract\Auth;

interface AuthFactory
{
    /**
     * 按名称获取实例.
     */
    public function guard(?string $name = null): Guard;

    /**
     * 设置工厂应提供的默认实例.
     */
    public function shouldUse(string $name): void;
}
