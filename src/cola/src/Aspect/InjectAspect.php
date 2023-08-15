<?php

namespace MaliBoot\Cola\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Cola\Annotation\AggregateRoot;
use MaliBoot\Cola\Annotation\Database;
use MaliBoot\Cola\Annotation\DataObject;
use MaliBoot\Cola\Annotation\DomainObject;
use MaliBoot\Cola\Annotation\Entity;
use MaliBoot\Cola\Annotation\ValueObject;

class InjectAspect extends AbstractAspect
{
    public array $annotations = [
        AggregateRoot::class,
        DomainObject::class,
        DataObject::class,
        Entity::class,
        ValueObject::class,
        Database::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // Do nothing, just to mark the class should be generated to the proxy classes.
        return $proceedingJoinPoint->process();
    }
}