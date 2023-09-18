<?php

declare(strict_types=1);

namespace MaliBoot\Auth;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Str;
use MaliBoot\Auth\Guard\JwtGuard;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\AuthFactory as AuthFactoryContract;
use MaliBoot\Contract\Auth\Guard;
use MaliBoot\Contract\Auth\UserProvider;
use MaliBoot\Request\Contract\RequestInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AuthFactory.
 * @method bool check()
 * @method bool guest()
 * @method null|Authenticatable user()
 * @method null|int|string id()
 * @method bool hasUser()
 * @method static setUser(Authenticatable $user)
 * @method mixed login(Authenticatable $user)
 * @method mixed loginUsingId(int|string $id)
 * @method mixed logout()
 * @mixin Guard
 */
class AuthFactory implements AuthFactoryContract
{
    /**
     * 创建的“驱动”数组.
     */
    protected array $guards = [];

    /**
     * 各种服务共享的用户解析器。
     */
    protected UserProvider|null $userResolver = null;

    /**
     * 创建新的身份验证管理器实例.
     */
    public function __construct(protected ConfigInterface $config, protected ContainerInterface $container)
    {
        $this->userResolver = $this->provider();
    }

    /**
     * 动态调用默认驱动程序实例.
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->guard()->{$method}(...$parameters);
    }

    /**
     * 尝试从本地缓存获取实例.
     */
    public function guard(?string $name = null): Guard
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    /**
     * 创建一个 JWT 验证器.
     *
     * @param string $name
     * @param array $config
     * @return JwtGuard
     */
    public function createJwtDriver($name, $config)
    {
        $request = $this->container->get(RequestInterface::class);
        try {
            $providerName = $this->getConfig($name)['provider'];
            $guard = make(JwtGuard::class, [$config, $name, $this->provider($providerName), $request]);
        } catch (\InvalidArgumentException $exception) {
            if (Str::contains($exception->getMessage(), 'Secret')) {
                throw new \InvalidArgumentException('Secret 未填写，请检查配置文件 config/autoload/auth.php 或环境变量 .env 中是否配置。');
            }

            throw $exception;
        }

        return $guard;
    }

    /**
     * 获取默认身份验证驱动名称.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('auth.default.guard');
    }

    /**
     * 获取默认身份验证提供者.
     *
     * @return string
     */
    public function getDefaultProvider()
    {
        return $this->config->get('auth.default.provider');
    }

    /**
     * 设置工厂应提供的默认驱动.
     */
    public function shouldUse(string $name): void
    {
        $name = $name ?: $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = $this->provider($name);
    }

    /**
     * 设置默认身份验证驱动名称.
     *
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->config->set('auth.default.guard', $name);
    }

    /**
     * 获取用户解析器回调.
     */
    public function userResolver(): UserProvider
    {
        return $this->userResolver;
    }

    /**
     * 设置用于解析用户的回调.
     *
     * @return $this
     */
    public function resolveUsersUsing(UserProvider $userResolver): static
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * 注册提供者创建者.
     */
    public function provider(?string $name = null): UserProvider
    {
        $name = $name ?: $this->getDefaultProvider();

        return $this->providers[$name] ?? $this->providers[$name] = $this->resolveProvider($name);
    }

    /**
     * 确定是否已解析任何实例。
     */
    public function hasResolvedGuards(): bool
    {
        return count($this->guards) > 0;
    }

    /**
     * 解析指定的实例.
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve(string $name): Guard
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new \InvalidArgumentException(
            "Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
        );
    }

    /**
     * 获取配置.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->config->get("auth.guards.{$name}");
    }

    /**
     * 解析指定的实例.
     *
     * @return Guard
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveProvider(string $name): UserProvider
    {
        $config = $this->config->get("auth.providers.{$name}");

        if (is_null($config)) {
            throw new \InvalidArgumentException("Auth provider [{$name}] is not defined.");
        }

        if (class_exists($config['provider'])) {
            return make($config['provider'], [$config, $name]);
        }

        throw new \InvalidArgumentException(
            "Auth provider [{$config['provider']}] for guard [{$name}] is not defined."
        );
    }
}
