<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Auth\Annotation\Auth;
use MaliBoot\Auth\Exception\UnauthorizedException;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\AuthFactory;
use MaliBoot\Di\Annotation\Inject;

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

            if (! $guard->user() instanceof Authenticatable) {
                throw new UnauthorizedException("Without authorization from {$name} guard", $guard);
            }
        }

        return $proceedingJoinPoint->process();
    }
}
