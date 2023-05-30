<?php

declare(strict_types=1);

namespace MaliBoot\ExceptionHandler\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\LoggerFactory;
use MaliBoot\Database\Contract\ResponseDbQueryDebug;
use MaliBoot\ErrorCode\Constants\ServerErrorCode;
use MaliBoot\ErrorCode\ErrorCodeCollector;
use MaliBoot\ResponseWrapper\SingleResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;

    public function __construct(
        protected LoggerFactory $loggerFactory,
        protected ConfigInterface $config
    ) {
        $this->logger = $loggerFactory->get(__CLASS__);
    }

    protected function formatError(\Throwable $throwable): array
    {
        $errCode = $throwable->getCode() !== 0 ? $throwable->getCode() : ServerErrorCode::SERVER_ERROR;
        if (! ErrorCodeCollector::hasCode($errCode)) {
            $errCode = ServerErrorCode::SERVER_ERROR;
        }

        if (empty($errMessage = $throwable->getMessage())) {
            $errMessage = ErrorCodeCollector::getMessage($errCode);
        }

        if ($this->config->get('app_debug')) {
            $errMessage = sprintf('%s[%s] in %s', $errMessage, $throwable->getLine(), $throwable->getFile());
        }

        return [$errCode, $errMessage];
    }

    protected function log(\Throwable $throwable, ResponseInterface $response): void
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());
    }

    protected function response($errCode, string $errMessage, \Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $errResponse = SingleResponse::buildFailure($errCode, $errMessage);
        if ($this->config->get('app_debug')) {
            $trace = array_reverse(explode("\n", $throwable->getTraceAsString()));
            $debugSql = [];
            if ($errResponse instanceof Response) {
                $debugSql = $response->getAttribute(ResponseDbQueryDebug::class, []);
            }
            $errResponse->setDebug(true)->setDebugTrace($trace)->setDebugSql($debugSql);
        }

        $this->stopPropagation();
        return $response->withHeader('Server', 'maliboot')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(ErrorCodeCollector::getStatusCode($errCode))
            ->withBody(new SwooleStream((string) $errResponse));
    }
}
