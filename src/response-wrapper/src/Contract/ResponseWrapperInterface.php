<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper\Contract;

use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Psr\Http\Message\ServerRequestInterface;

interface ResponseWrapperInterface
{
    /**
     * @param null|array|Arrayable|Jsonable|string $response
     */
    public function handle($response, ServerRequestInterface $request);
}
