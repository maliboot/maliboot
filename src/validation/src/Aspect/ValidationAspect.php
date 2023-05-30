<?php

declare(strict_types=1);

namespace MaliBoot\Validation\Aspect;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Cola\Annotation\AppService;
use MaliBoot\Dto\AbstractCommand;
use MaliBoot\Validation\Validator;

#[Aspect]
class ValidationAspect extends AbstractAspect
{
    public const METHOD_NAME = 'execute';

    public array $annotations = [
        AppService::class,
    ];

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if ($proceedingJoinPoint->methodName === self::METHOD_NAME) {
            $arguments = $proceedingJoinPoint->getArguments();
            if (! empty($arguments) && $arguments[0] instanceof AbstractCommand) {
                $this->validated($arguments[0]);
            }
        }

        return $proceedingJoinPoint->process();
    }

    protected function validated(AbstractCommand $cmd): bool
    {
        $validator = $this->container->get(Validator::class);
        return $validator->validated($cmd);
    }
}
