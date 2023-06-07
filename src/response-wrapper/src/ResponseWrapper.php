<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

use Hyperf\Context\Context;
use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;
use MaliBoot\Database\Contract\ResponseDbQueryDebug;
use MaliBoot\Dto\AbstractViewObject;
use MaliBoot\Dto\EmptyVO;
use MaliBoot\Dto\PageVO;
use MaliBoot\ResponseWrapper\Contract\ResponseWrapperInterface;
use MaliBoot\Utils\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResponseWrapper implements ResponseWrapperInterface
{
    /**
     * @param null|AbstractViewObject|array|Arrayable|Jsonable|string $response
     */
    public function handle($response, ServerRequestInterface $request)
    {
        if (! is_null($response) && ! $this->isVO($response)) {
            return $response;
        }

        if ($response instanceof PageVO) {
            $data = PageResponse::of($response);
        } elseif ($response instanceof Collection && $response[0] instanceof AbstractViewObject) {
            $data = MultiResponse::of($response);
        } elseif (is_null($response) || $response instanceof EmptyVO) {
            $data = SingleResponse::buildSuccess();
        } else {
            $data = SingleResponse::of($response);
        }

        $responseServer = $this->response();
        if (config('app_debug', false) && config('app_env', 'production') !== 'production') {
            /** @var Response $responseServer */
            if ($responseServer instanceof Response && $data instanceof \MaliBoot\ResponseWrapper\Response) {
                $debugSql = $responseServer->getAttribute(ResponseDbQueryDebug::class, []);
                $data->setDebug(true)->setDebugSql($debugSql);
            }
        }
        return $responseServer
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream((string) $data));
    }

    protected function isVO($response): bool
    {
        return $response instanceof PageVO
            || $response instanceof AbstractViewObject
            || ($response instanceof Collection && $response[0] instanceof AbstractViewObject)
            || (is_array($response) && isset($response[0]) && $response[0] instanceof AbstractViewObject)
            || ($response instanceof Arrayable && $response[0] instanceof AbstractViewObject)
            || ($response instanceof \MaliBoot\Utils\Contract\Arrayable && $response[0] instanceof AbstractViewObject);
    }

    /**
     * Get response instance from context.
     */
    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}
