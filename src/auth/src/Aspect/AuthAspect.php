<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Aspect;

use Hyperf\Context\Context;
use Hyperf\Contract\Arrayable;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Auth\Annotation\Auth;
use MaliBoot\Auth\Exception\UnauthorizedException;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\AuthFactory;
use MaliBoot\Di\Annotation\Inject;
use MaliBoot\Dto\UserContext;
use MaliBoot\Request\Contract\RequestInterface;
use MaliBoot\Utils\ObjectUtil;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthAspect.
 */
class AuthAspect extends AbstractAspect
{
    public array $annotations = [
        Auth::class,
    ];

    #[Inject]
    protected AuthFactory $auth;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $annotation = $proceedingJoinPoint->getAnnotationMetadata();

        /** @var Auth $authAnnotation */
        $authAnnotation = $annotation->class[Auth::class] ?? $annotation->method[Auth::class];

        $guards = is_array($authAnnotation->value) ? $authAnnotation->value : [$authAnnotation->value];

        foreach ($guards as $name) {
            $guard = $this->auth->guard($name);

            if (! ($user = $guard->user()) instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$name} guard", $guard);
            }

            $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($user){
                return $request->withAttribute('user', $user);
            });

            Context::set(RequestInterface::class, $request);

            $arguments = $proceedingJoinPoint->getArguments();
            if (ObjectUtil::isDTO($arguments[0])) {
                $this->fillUserToDTO($user, $arguments[0]);
            }
        }

        return $proceedingJoinPoint->process();
    }

    protected function fillUserToDTO(Authenticatable $user, object $dto): object
    {
        if (! $user instanceof UserContext) {
            $userContext = new UserContext();

            if ($user instanceof Arrayable
                || $user instanceof \MaliBoot\Utils\Contract\Arrayable
                || (is_object($user) && method_exists($user, 'toArray'))
            ) {
                $user = $user->toArray();
            }

            $userContext->initData((array) $user);
        } else {
            $userContext = $user;
        }

        $dto->setUser($userContext);
        return $dto;
    }
}
