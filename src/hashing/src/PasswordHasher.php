<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class PasswordHasher extends AbstractHasher
{
    public function signature(string $value): string
    {
        return password_hash(md5($value . $this->getSecret()), PASSWORD_BCRYPT);
    }

    public function check(string $value, string $hashedValue): bool
    {
        return password_verify(md5($value . $this->getSecret()), $hashedValue);
    }

    public static function alg(): string
    {
        return 'password_hash';
    }
}
