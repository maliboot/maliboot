<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class SHA1Hash extends AbstractHasher
{
    public function signature(string $value): string
    {
        return hash('sha1', $value . $this->getSecret());
    }

    public static function alg(): string
    {
        return 'sha1';
    }
}
