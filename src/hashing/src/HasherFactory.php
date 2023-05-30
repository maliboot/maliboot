<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Arr;
use MaliBoot\Contract\Hashing\Hasher;
use Psr\Container\ContainerInterface;

class HasherFactory
{
    protected array $hasherList = [
        'php-crypt' => CryptHasher::class,
        'HS256' => HS256Hasher::class,
        'md5' => Md5Hasher::class,
        'password_hash' => PasswordHasher::class,
        'sha1' => SHA1Hash::class,
    ];

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class)->get('hashing');
        $driver = Arr::get($config, 'driver', 'password_hash');
        $secret = Arr::get($config, 'secret', 'maliboot');
        return $this->createDriver($driver, $secret);
    }

    protected function createDriver(string $driver, string $secret): Hasher
    {
        if (array_key_exists($driver, $this->hasherList)) {
            return make($this->hasherList[$driver], [$secret]);
        }

        throw new \InvalidArgumentException("Driver [{$driver}] not supported.");
    }
}
