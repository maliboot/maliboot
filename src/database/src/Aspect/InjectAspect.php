<?php

namespace MaliBoot\Database\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use MaliBoot\Database\Annotation\BelongsTo;
use MaliBoot\Database\Annotation\BelongsToMany;
use MaliBoot\Database\Annotation\Cast;
use MaliBoot\Database\Annotation\Column;
use MaliBoot\Database\Annotation\DB;
use MaliBoot\Database\Annotation\HasMany;
use MaliBoot\Database\Annotation\HasOne;
use MaliBoot\Database\Annotation\PrimaryKey;

class InjectAspect extends AbstractAspect
{
    public array $annotations = [
        Column::class,
        PrimaryKey::class,
        DB::class,
        HasOne::class,
        HasMany::class,
        BelongsTo::class,
        BelongsToMany::class,
        Cast::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // Do nothing, just to mark the class should be generated to the proxy classes.
        return $proceedingJoinPoint->process();
    }
}