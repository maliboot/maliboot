<?php

declare(strict_types=1);

namespace MaliBoot\Plugin\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Framework\Event\BootApplication;
use MaliBoot\Plugin\AbstractPlugin;
use MaliBoot\Utils\Composer;
use Symfony\Component\Finder\Finder;

abstract class AbstractListener
{
    public function __construct(protected ConfigInterface $config, protected ContainerInterface $container)
    {
    }

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $dependencies = [];
        $pluginPath = $this->config->get('plugin.paths.base_path', BASE_PATH . '/plugin');
        $finder = new Finder();
        $finder->directories()->in($pluginPath);
        foreach ($finder as $dir) {
            $providers = Composer::getMergedExtra('hyperf', $dir->getPath())['config'] ?? [];
            foreach ($providers as $provider) {
                if (is_string($provider) && class_exists($provider) && is_subclass_of($provider, AbstractPlugin::class)) {
                    $dependencies = array_merge($this->execute(new $provider()), $dependencies);
                }
            }
        }

        if (empty($dependencies)) {
            return;
        }

        $this->addDependencies($dependencies);
    }

    abstract protected function execute($plugin): array;

    protected function addDependencies(array $dependencies): void
    {
        // 需主动向容器中定义依赖项
        foreach ($dependencies as $name => $definition) {
            $this->container->define($name, $definition);
        }

        $this->config->set('dependencies', array_merge($this->config->get('dependencies', []), $dependencies));
    }

    protected function getNamespacePrefix(string $dir): string
    {
        $autoloadRules = data_get(Composer::getJsonContent(realpath($dir . '/..')), 'autoload.psr-4', []);
        foreach ($autoloadRules as $prefix => $prefixPath) {
            if ($prefixPath === 'src/') {
                return $prefix;
            }
        }

        return '';
    }
}
