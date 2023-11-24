<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use MaliBoot\Lombok\Contract\OfAnnotationInterface;
use MaliBoot\Lombok\Contract\ToArrayAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ORM implements OfAnnotationInterface, ToArrayAnnotationInterface
{
    /**
     * Laravel-ORM 管理，如字段映射、模型关系定义、模型事件......
     * @param null|string $name 数据库字段名称。默认为类属性名称的蛇形命名.
     */
    public function __construct(
        public ?string $name = null,
    ) {}
}
