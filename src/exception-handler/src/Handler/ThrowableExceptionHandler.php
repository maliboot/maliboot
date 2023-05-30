<?php

declare(strict_types=1);

namespace MaliBoot\ExceptionHandler\Handler;

use Psr\Http\Message\ResponseInterface;

class ThrowableExceptionHandler extends AbstractExceptionHandler
{
    /**
     * @return mixed
     */
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->log($throwable, $response);
        [$errCode, $errMessage] = $this->formatError($throwable);
        return $this->response($errCode, $errMessage, $throwable, $response);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof \Throwable;
    }
}
