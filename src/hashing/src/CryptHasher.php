<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class CryptHasher extends AbstractHasher
{
    public function signature(string $value): string
    {
        return crypt($value, $this->getSecret());
    }

    public static function alg(): string
    {
        return 'php-crypt';
    }
}
