<?php

declare(strict_types=1);

namespace MaliBoot\Contract\Hashing;

interface Hasher
{
    public function signature(string $value): string;

    public function check(string $value, string $hashedValue): bool;

    public static function alg(): string;
}
