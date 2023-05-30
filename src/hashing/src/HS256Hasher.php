<?php

declare(strict_types=1);

namespace MaliBoot\Hashing;

class HS256Hasher extends AbstractHasher
{
    public function signature(string $value): string
    {
        return \hash_hmac('SHA256', $value, $this->getSecret(), true);
    }

    public function check(string $value, string $hashedValue): bool
    {
        $hash = \hash_hmac('SHA256', $value, $this->getSecret(), true);
        if (\function_exists('hash_equals')) {
            return \hash_equals($hashedValue, $hash);
        }
        $len = \min(static::safeStrlen($hashedValue), static::safeStrlen($hash));

        $status = 0;
        for ($i = 0; $i < $len; ++$i) {
            $status |= (\ord($hashedValue[$i]) ^ \ord($hash[$i]));
        }
        $status |= (static::safeStrlen($hashedValue) ^ static::safeStrlen($hash));

        return $status === 0;
    }

    public static function alg(): string
    {
        return 'HS256';
    }
}
