<?php

declare(strict_types=1);

namespace MaliBoot\Auth;

use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Lombok\Contract\HttpMessageDelegateInterface;

class UserIdData implements HttpMessageDelegateInterface
{
    public static function compute(string $key, array $attributes, object $instance, string $fieldName): mixed
    {
        if (empty($attributes['user']) || ! ($attributes['user'] instanceof Authenticatable)) {
            return null;
        }

        return $attributes['user']->getAuthIdentifier();
    }
}
