<?php

declare(strict_types=1);

return [
    'default' => [
        'guard' => 'jwt',
        'provider' => 'user',
    ],
    'guards' => [
        'jwt' => [
            'driver' => \MaliBoot\Auth\Guard\JwtGuard::class,
            'provider' => 'user',
            'secret' => env('AUTH_JWT_SECRET'), // jwt 服务端身份标识
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'), // 请求头名称
            'ttl' => (int) env('AUTH_JWT_TTL', 60 * 60 * 24), // 有效期，单位秒，默认1天
            'refresh_ttl' => (int) env('AUTH_JWT_REFRESH_TTL', 60 * 60 * 24 * 7), // 刷新有效期，单位秒，默认一周
            'prefix' => env('AUTH_JWT_PREFIX', 'maliboot'), // 缓存前缀
            /*
             * 可选配置
             * 缓存类
             */
            'cache' => function () {
                return make(\MaliBoot\Auth\Cache\RedisCache::class);
            },
        ],
    ],
    'providers' => [
        'user' => [
            'driver' => \MaliBoot\Auth\Provider\RpcUserProvider::class,
            'rpc' => \MaliPlugin\User\Client\Api\UserService::class, // 需要实现 MaliBoot\Contract\Auth\Authenticatable 接口
        ],
    ],
];
