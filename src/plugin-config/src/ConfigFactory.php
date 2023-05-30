<?php

declare(strict_types=1);

namespace MaliBoot\PluginConfig;

use Hyperf\Config\Config;
use Hyperf\Config\ProviderConfig;
use Hyperf\Utils\Arr;
use MaliBoot\Utils\File;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $configPath = BASE_PATH . '/config';
        $config = $this->readConfig($configPath . '/config.php');
        $autoloadConfig = $this->readPaths([$configPath . '/autoload']);
        $merged = array_merge_recursive(ProviderConfig::load(), $config, ...$autoloadConfig);

        $pluginPath = Arr::get($merged, 'plugin.paths.base_path', BASE_PATH . '/plugin');
        $pluginMerged = $this->getPluginConfig($pluginPath);
        $merged = array_merge_recursive($merged, $pluginMerged);
        return new Config($merged);
    }

    private function readConfig(string $configPath): array
    {
        $config = [];
        if (file_exists($configPath) && is_readable($configPath)) {
            $config = require $configPath;
        }
        return is_array($config) ? $config : [];
    }

    private function readPaths(array $paths): array
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[] = [
                $file->getBasename('.php') => require $file->getRealPath(),
            ];
        }
        return $configs;
    }

    private function getPluginConfig(string $pluginPath): array
    {
        $configs = [];

        if (! File::exists($pluginPath)) {
            return $configs;
        }

        $finder = new Finder();
        $finder->directories()->in($pluginPath);
        foreach ($finder as $dir) {
            $configKey = $this->getPluginConfigKey($this->getPluginName($dir->getPath()));
            if (! File::exists($dir->getPath() . '/config')) {
                continue;
            }
            $pluginConfig = [
                $configKey => $this->readConfig($dir->getPath() . '/config/config.php'),
            ];
            $pluginAutoloadConfig = $this->readPluginPaths($configKey, [$dir->getPath() . '/config']);
            $configs = array_merge_recursive($configs, $pluginConfig, $pluginAutoloadConfig);
        }

        return $configs;
    }

    private function getPluginName(string $path): string
    {
        $paths = explode('/', trim($path, '/'));
        return array_pop($paths);
    }

    private function getPluginConfigKey(string $pluginName): string
    {
        return $pluginName;
    }

    private function readPluginPaths(string $pluginConfigKey, array $paths): array
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php')->notName('config.php');
        foreach ($finder as $file) {
            $configs = array_merge_recursive($configs, [
                $file->getBasename('.php') => require $file->getRealPath(),
            ]);
        }
        return [$pluginConfigKey => $configs];
    }
}
