<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Aspect;

use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Auth\Annotation\Auth;
use MaliBoot\Auth\Exception\UnauthorizedException;
use MaliBoot\Contract\Auth\AuthFactory;
use MaliBoot\Di\Annotation\Inject;
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
        $user = ApplicationContext::getContainer()->get(ServerRequestInterface::class)?->getAttribute('user');
        if (empty($user)) {
            throw new UnauthorizedException('Without authorization from guard');
        }

        return $proceedingJoinPoint->process();
    }
}
