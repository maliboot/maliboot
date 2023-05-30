<?php

declare(strict_types=1);

namespace MaliBoot\Validation\Exception\Handler;

use Hyperf\Validation\ValidationException;
use MaliBoot\ErrorCode\Constants\ServerErrorCode;
use MaliBoot\ExceptionHandler\Handler\AbstractExceptionHandler;
use Psr\Http\Message\ResponseInterface;

class ValidationExceptionHandler extends AbstractExceptionHandler
{
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        $this->log($throwable, $response);
        [$errCode, $errMessage] = $this->formatError($throwable);
        return $this->response($errCode, $errMessage, $throwable, $response);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }

    protected function formatError(\Throwable $throwable): array
    {
        /** @var ValidationException $throwable */
        $errCode = ServerErrorCode::INVALID_PARAMS;
        $errMessage = join(', ', $throwable->validator->errors()->all());

        return [$errCode, $errMessage];
    }
}
