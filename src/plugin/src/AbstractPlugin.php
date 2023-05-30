<?php

declare(strict_types=1);

namespace MaliBoot\Plugin;

use MaliBoot\Utils\Composer;

abstract class AbstractPlugin
{
    protected bool $useLocalService = true;

    protected static string $dir = __DIR__;

    public function __invoke(): array
    {
        return $this->config();
    }

    /**
     * 获取当前插件名称.
     */
    public static function getName(): string
    {
        $composerJson = Composer::getJsonContent(realpath(static::$dir . '/..'));
        return explode('/', $composerJson['name'])[1];
    }

    public function getUseLocalService(): bool
    {
        return $this->useLocalService;
    }

    public function setUseLocalService(bool $useLocalService): static
    {
        $this->useLocalService = $useLocalService;
        return $this;
    }

    public static function getDir(): string
    {
        return static::$dir;
    }

    abstract protected function config(): array;
}
