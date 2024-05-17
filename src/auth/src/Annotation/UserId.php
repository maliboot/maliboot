<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Auth\UserIdData;
use MaliBoot\Lombok\Contract\HttpMessageAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UserId extends AbstractAnnotation implements HttpMessageAnnotationInterface
{
    public function __construct() {}

    public function type(): int
    {
        return HttpMessageAnnotationInterface::ATTRIBUTE;
    }

    public function delegate(): string
    {
        return UserIdData::class;
    }
}
