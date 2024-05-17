<?php

declare(strict_types=1);

namespace MaliBoot\Auth;

use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Aop\AnnotationMetadata;
use Hyperf\HttpServer\CoreMiddleware;
use MaliBoot\Auth\Annotation\Auth;
use MaliBoot\Cola\Adapter\ControllerDispatchEventInterface;
use MaliBoot\Cola\Annotation\ControllerDispatchEvent;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Request\Contract\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

#[ControllerDispatchEvent]
class AuthUserInject implements ControllerDispatchEventInterface
{
    public static function dispatchBefore(CoreMiddleware $coreMiddleware, string $controller, string $action, array $arguments)
    {
        $annotation = self::getAnnotationMetadata($controller, $action);
        /** @var Auth $authAnnotation */
        $authAnnotation = $annotation->class[Auth::class] ?? $annotation->method[Auth::class];

        $guards = is_array($authAnnotation->value) ? $authAnnotation->value : [$authAnnotation->value];
        $container = ApplicationContext::getContainer();
        $auth = $container->get(AuthFactory::class);
        foreach ($guards as $name) {
            $guard = $auth->guard($name);

            if (! ($user = $guard->user()) instanceof Authenticatable) {
                continue;
            }

            $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($user) {
                return $request->withAttribute('user', $user);
            });

            Context::set(RequestInterface::class, $request);
        }
    }

    protected static function getAnnotationMetadata(string $controller, string $action): AnnotationMetadata
    {
        $metadata = AnnotationCollector::get($controller);
        return new AnnotationMetadata($metadata['_c'] ?? [], $metadata['_m'][$action] ?? []);
    }
}
