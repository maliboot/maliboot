<?php

declare(strict_types=1);

namespace MaliBoot\Auth;

use MaliBoot\Auth\Exception\UnauthorizedException;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\AuthFactory;
use MaliBoot\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthMiddleware.
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected array $guards = [null];

    #[Inject(AuthFactory::class)]
    protected AuthFactory $auth;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->guards as $name) {
            $guard = $this->auth->guard($name);

            if (! $guard->user() instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$name} guard", $guard);
            }
        }

        return $handler->handle($request);
    }
}
