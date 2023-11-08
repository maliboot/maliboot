<?php

declare(strict_types=1);

namespace MaliBoot\Database\Listener;

use Hyperf\Collection\Arr;
use Hyperf\Context\Context;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\Stringable\Str;
use MaliBoot\Database\Contract\ResponseDbQueryDebug;
use Psr\Http\Message\ResponseInterface;

class DbQueryExecutedDebugListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param QueryExecuted $event ...
     */
    public function process(object $event): void
    {
        if (! ($event instanceof QueryExecuted) || ! config('app_debug', false)) {
            return;
        }

        /** @var Response $response */
        $response = Context::get(ResponseInterface::class);
        if (! $response instanceof Response) {
            return;
        }

        $sql = $event->sql;
        if (! Arr::isAssoc($event->bindings)) {
            foreach ($event->bindings as $value) {
                $sql = Str::replaceFirst('?', "'{$value}'", $sql);
            }
        }
        $debugSql = $response->getAttribute(ResponseDbQueryDebug::class, []);
        $debugSql[] = ['time' => $event->time, 'query' => $sql];
        function_exists('dump') && dump($debugSql);
        Context::set(ResponseInterface::class, $response->withAttribute(ResponseDbQueryDebug::class, $debugSql));
    }
}
