<?php

declare(strict_types=1);
if (! function_exists('read_file_name')) {
    /**
     * 取出某目录下所有php文件的文件名.
     * @param string $path 文件夹目录
     * @return array 文件名
     */
    function read_file_name(string $path): array
    {
        $data = [];
        if (! is_dir($path)) {
            return $data;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..', '.DS_Store'])) {
                continue;
            }
            $data[] = preg_replace('/(\w+)\.php/', '$1', $file);
        }
        return $data;
    }
}

if (! function_exists('impl_bind')) {
    /**
     * 实现与接口进行绑定.
     */
    function impl_bind(string $path, string $namespacePrefix): array
    {
        $namespacePrefix = ltrim($namespacePrefix, '\\');
        $dependencies = [];

        $services = read_file_name($path . '/App/Service');
        foreach ($services as $service) {
            $contractFilename = str_replace('Impl', '', $service);
            $dependencies[$namespacePrefix . '\\Client\\Api\\' . $contractFilename] = $namespacePrefix . '\\App\\Service\\' . $service;
        }

        $gateways = read_file_name($path . '/Infrastructure/GatewayImpl');
        foreach ($gateways as $gateway) {
            if (strpos($gateway, 'Impl') === false) {
                continue;
            }

            $contractFilename = str_replace('Impl', '', $gateway);
            $dependencies[$namespacePrefix . '\\Domain\\Gateway\\' . $contractFilename] = $namespacePrefix . '\\Infrastructure\\GatewayImpl\\' . $gateway;
        }

        return $dependencies;
    }
}

if (! function_exists('module_impl_bind')) {
    /**
     * 模块中实现与接口绑定.
     */
    function module_impl_bind(string $path, string $namespacePrefix = 'Module\\'): array
    {
        $dependencies = [];
        if (! is_dir($path)) {
            return $dependencies;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..', '.DS_Store'])) {
                continue;
            }

            if (! is_dir($path . '/' . $file . '/src')) {
                continue;
            }
            $arr = explode('-', $file);
            array_walk($arr, function (&$val) {
                $val = ucfirst($val);
            });
            $namespace = $namespacePrefix . implode('', $arr);
            $dependencies = array_merge(impl_bind($path . '/' . $file . '/src', $namespace), $dependencies);
        }
        return $dependencies;
    }
}
