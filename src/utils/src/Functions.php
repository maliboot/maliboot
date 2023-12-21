<?php

declare(strict_types=1);

if (! function_exists('env')) {

    /**
     * 获取环境变量信息
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Support\env($key, $default);
    }

}

if (! function_exists('config')) {

    /**
     * 获取配置信息
     *
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Config\config($key, $default);
    }

}

if (! function_exists('make')) {

    /**
     * Create an object instance, if the DI container exist in ApplicationContext,
     * then the object will be created by DI container via `make()` method, if not,
     * the object will create by `new` keyword.
     */
    function make(string $name, array $parameters = [])
    {
        return \Hyperf\Support\make($name, $parameters);
    }

}

if (! function_exists('di')) {

    /**
     * 获取容器实例
     * @param string $id
     * @return \Psr\Container\ContainerInterface|mixed
     */
    function di(?string $id = null): mixed
    {
        if (is_null($id)) {
            return \Hyperf\Context\ApplicationContext::getContainer();
        }

        return \Hyperf\Context\ApplicationContext::getContainer()->get($id);
    }

}