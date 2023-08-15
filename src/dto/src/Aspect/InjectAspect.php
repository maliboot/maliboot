<?php

namespace MaliBoot\Dto\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Cola\Annotation\ValueObject;
use MaliBoot\Dto\Annotation\Context;
use MaliBoot\Dto\Annotation\DataTransferObject;
use MaliBoot\Dto\Annotation\ViewObject;

class InjectAspect extends AbstractAspect
{
    public array $annotations = [
        DataTransferObject::class,
        ValueObject::class,
        Context::class,
        ViewObject::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // Do nothing, just to mark the class should be generated to the proxy classes.
        return $proceedingJoinPoint->process();
    }
}