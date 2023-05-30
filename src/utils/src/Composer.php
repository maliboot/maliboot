<?php

declare(strict_types=1);

namespace MaliBoot\Utils;

use Composer\Autoload\ClassLoader;

class Composer
{
    private static array $content = [];

    private static array $json = [];

    private static array $extra = [];

    private static array $scripts = [];

    private static array $versions = [];

    private static ?ClassLoader $classLoader = null;

    /**
     * @param mixed $path
     * @throws \RuntimeException When `composer.lock` does not exist.
     */
    public static function getLockContent($path = BASE_PATH): Collection
    {
        $arrKey = self::getKey($path);
        if (! self::$content || ! isset(self::$content[$arrKey])) {
            $path = self::discoverLockFile();
            if (! $path) {
                throw new \RuntimeException('composer.lock not found.');
            }
            self::$content[$arrKey] = new Collection(json_decode(file_get_contents($path), true));
            $packages = self::$content[$arrKey]->offsetGet('packages') ?? [];
            $packagesDev = self::$content[$arrKey]->offsetGet('packages-dev') ?? [];
            foreach (array_merge($packages, $packagesDev) as $package) {
                $packageName = '';
                foreach ($package ?? [] as $key => $value) {
                    if ($key === 'name') {
                        $packageName = $value;
                        continue;
                    }
                    switch ($key) {
                        case 'extra':
                            $packageName && self::$extra[$arrKey][$packageName] = $value;
                            break;
                        case 'scripts':
                            $packageName && self::$scripts[$arrKey][$packageName] = $value;
                            break;
                        case 'version':
                            $packageName && self::$versions[$arrKey][$packageName] = $value;
                            break;
                    }
                }
            }
        }
        return self::$content[$arrKey];
    }

    public static function getJsonContent($path = BASE_PATH): Collection
    {
        $key = self::getKey($path);
        if (! self::$json || ! isset(self::$json[$key])) {
            $jsonPath = $path . '/composer.json';
            if (! is_readable($jsonPath)) {
                throw new \RuntimeException($path . '/composer.json is not readable.');
            }
            self::$json[$key] = new Collection(json_decode(file_get_contents($jsonPath), true));
        }

        return self::$json[$key];
    }

    public static function discoverLockFile($path = BASE_PATH): string
    {
        $lockPath = '';
        if (is_readable($path . '/composer.lock')) {
            $lockPath = $path . '/composer.lock';
        }
        return $lockPath;
    }

    public static function getMergedExtra(string $key = null, $path = BASE_PATH)
    {
        $arrKey = self::getKey($path);
        if (! self::$extra || ! isset(self::$extra[$arrKey])) {
            self::getLockContent($path);
        }
        if ($key === null) {
            return self::$extra[$arrKey];
        }
        $extra = [];
        foreach (self::$extra[$arrKey] as $project => $config) {
            foreach ($config ?? [] as $configKey => $item) {
                if ($key === $configKey && $item) {
                    foreach ($item as $k => $v) {
                        if (is_array($v)) {
                            $extra[$k] = array_merge($extra[$k] ?? [], $v);
                        } else {
                            $extra[$k][] = $v;
                        }
                    }
                }
            }
        }
        return $extra;
    }

    public static function getLoader(): ClassLoader
    {
        if (! self::$classLoader) {
            self::$classLoader = self::findLoader();
        }
        return self::$classLoader;
    }

    public static function setLoader(ClassLoader $classLoader): ClassLoader
    {
        self::$classLoader = $classLoader;
        return $classLoader;
    }

    public static function getScripts(): array
    {
        return self::$scripts;
    }

    public static function getVersions(): array
    {
        return self::$versions;
    }

    private static function findLoader(): ClassLoader
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                return $loader[0];
            }
        }

        throw new \RuntimeException('Composer loader not found.');
    }

    private static function getKey($path): string
    {
        return md5($path);
    }
}
