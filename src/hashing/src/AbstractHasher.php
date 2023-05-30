<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

use MaliBoot\Contract\Hashing\Hasher;

abstract class AbstractHasher implements Hasher
{
    public function __construct(protected string $secret)
    {
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function check(string $value, string $hashedValue): bool
    {
        return $this->signature($value) === $hashedValue;
    }

    /**
     * Get the number of bytes in cryptographic strings.
     *
     * @param string $str
     *
     * @return int
     */
    public static function safeStrlen($str)
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, '8bit');
        }
        return \strlen($str);
    }
}
