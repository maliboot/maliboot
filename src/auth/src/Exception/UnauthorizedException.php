<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Exception;

use MaliBoot\Contract\Auth\Guard;

class UnauthorizedException extends AuthenticationException
{
    protected ?Guard $guard;

    protected int $statusCode = 401;

    public function __construct(string $message, Guard $guard = null, \Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
        $this->guard = $guard;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
