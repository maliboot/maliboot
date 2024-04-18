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
        $errCode = $throwable->getCode();
        $errMessage = $throwable->getMessage();
        if ($errCode === 0) {
            $errCode = ServerErrorCode::SERVER_ERROR;
        }

        if (ErrorCodeCollector::hasCode($errCode)) {
            $errMessage = ErrorCodeCollector::getMessage($errCode);
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
            $errResponse->setDebug(true)
                ->setDebugTrace($trace)
                ->setDebugSql($debugSql)
                ->setDebugError(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }

        $this->stopPropagation();
        return $response->withHeader('Server', 'maliboot')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(ErrorCodeCollector::getStatusCode($errCode))
            ->withBody(new SwooleStream((string) $errResponse));
    }
}
