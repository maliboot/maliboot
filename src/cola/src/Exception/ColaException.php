<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Exception;

class ColaException extends \Exception
{
    public function __construct(int $code = 0, string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
