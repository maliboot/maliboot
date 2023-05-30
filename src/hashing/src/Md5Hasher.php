<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class Md5Hasher extends AbstractHasher
{
    public function signature(string $value): string
    {
        return hash('md5', $value . $this->getSecret());
    }

    public static function alg(): string
    {
        return 'md5';
    }
}
