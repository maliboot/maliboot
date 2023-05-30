<?php

declare(strict_types=1);

namespace MaliBoot\Plugin\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Symfony\Component\Finder\Finder;

class BindImplListener extends AbstractListener implements ListenerInterface
{
    protected function execute($plugin): array
    {
        return array_merge($this->bindRepository($plugin), $this->registerService($plugin));
    }

    protected function bindRepository($plugin): array
    {
        $dependencies = [];
        $namespacePrefix = $this->getNamespacePrefix($plugin::getDir());
        $finder = new Finder();
        $finder = $finder->files()->in($plugin::getDir() . '/Domain/Repository')->name('*Repo.php');
        foreach ($finder as $file) {
            $repositoryName = $file->getBasename('.php');
            $repositoryImplClassName = sprintf('%sInfra\\Repository\\%sCmdRepo', $namespacePrefix, str_replace('Repo', '', $repositoryName));
            if (! class_exists($repositoryImplClassName)) {
                continue;
            }

            $repositoryClassName = sprintf('%sDomain\\Repository\\%s', $namespacePrefix, $repositoryName);
            $dependencies[$repositoryClassName] = $repositoryImplClassName;
        }

        return $dependencies;
    }

    protected function registerService($plugin): array
    {
        if ($plugin->getUseLocalService()) {
            return $this->registerLocalService($plugin);
        }
        return $this->registerRpcService($plugin);
    }

    protected function registerLocalService($plugin): array
    {
        $dependencies = [];
        $namespacePrefix = $this->getNamespacePrefix($plugin::getDir());
        $finder = new Finder();
        $finder = $finder->files()->in($plugin::getDir() . '/Client/Api')->name('*Service.php');
        foreach ($finder as $file) {
            $serviceName = str_replace('Service', '', $file->getBasename('.php'));
            $serviceImplClassName = sprintf('%sAdapter\\Rpc\\%sRpcService', $namespacePrefix, $serviceName);
            if (! class_exists($serviceImplClassName)) {
                continue;
            }

            $serviceClassName = sprintf('%sClient\\Api\\%sService', $namespacePrefix, $serviceName);
            $dependencies[$serviceClassName] = $serviceImplClassName;
        }

        return $dependencies;
    }

    protected function registerRpcService($plugin): array
    {
        return [];
    }
}
