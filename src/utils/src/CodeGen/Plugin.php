<?php

declare(strict_types=1);

namespace MaliBoot\Utils\CodeGen;

use Hyperf\Utils\Str;
use MaliBoot\Utils\Composer;

/**
 * Read composer.json autoload psr-4 rules to figure out the namespace or path.
 */
class Plugin
{
    public function __construct(protected string $pluginName = '')
    {
    }

    public function namespace(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext !== '') {
            $path = substr($path, 0, -(strlen($ext) + 1));
        } else {
            $path = trim($path, '/') . '/';
        }

        $path = $this->getPluginPath($path);
        foreach ($this->getAutoloadRules() as $prefix => $prefixPath) {
            if ($this->isRootNamespace($prefix) || str_starts_with($path, $prefixPath)) {
                return $prefix . str_replace('/', '\\', substr($path, strlen($prefixPath)));
            }
        }
        throw new \RuntimeException("Invalid project path: {$path}");
    }

    public function className(string $path): string
    {
        return $this->namespace($path);
    }

    public function path(string $name, $extension = '.php'): string
    {
        if (Str::endsWith($name, '\\')) {
            $extension = '';
        }

        $name = $this->getPluginPath($name);
        foreach ($this->getAutoloadRules() as $prefix => $prefixPath) {
            if ($this->isRootNamespace($prefix) || str_starts_with($name, $prefix)) {
                return $prefixPath . str_replace('\\', '/', substr($name, strlen($prefix))) . $extension;
            }
        }

        throw new \RuntimeException("Invalid class name: {$name}");
    }

    protected function isRootNamespace(string $namespace): bool
    {
        return $namespace === '';
    }

    protected function getAutoloadRules(): array
    {
        if ($this->pluginName) {
            $path = rtrim(config('plugin.paths.base_path', 'plugin'), '/') . '/' . $this->pluginName;
        } else {
            $path = BASE_PATH;
        }

        return data_get(Composer::getJsonContent($path), 'autoload.psr-4', []);
    }

    protected function getPluginPath(string $path): string
    {
        $pathPrefix = rtrim(config('plugin.paths.base_path', 'plugin'), '/') . '/' . $this->pluginName . '/';
        return str_replace(str_replace(BASE_PATH . '/', '', $pathPrefix), '', $path);
    }
}
