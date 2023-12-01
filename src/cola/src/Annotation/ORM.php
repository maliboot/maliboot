<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;
use MaliBoot\Lombok\Contract\OfAnnotationInterface;
use MaliBoot\Lombok\Contract\ToArrayAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ORM implements OfAnnotationInterface, ToArrayAnnotationInterface
{
    /**
     * Laravel-ORM 管理，如字段映射、模型关系定义...
     * @param null|string $name 数据库字段名称。默认为类属性名称的蛇形命名.
     * @param null|string $cast 数据类型映射
     * @param null|string $concern 模型关系-类型，支持：hasOne, hasMany, belongsTo, belongsToMany
     * @param null|string $foreignKey 模型关系-当前表在关联表OR中间表的外键名称. <span style="color:red"><strong>模型关联必填</strong></span>.
     * @param null|string $pivotTable 模型关系-中间表名称. <span style="color:red"><strong>多对多模型关联必填</strong></span>.
     * @param null|string $pivotForeignKey 模型关系-关联表在中间表的外键名称.<span style="color:red"><strong>多对多模型关联必填</strong></span>.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $cast = null,
        #[ExpectedValues(['hasOne', 'hasMany', 'belongsTo', 'belongsToMany'])]
        public ?string $concern = null,
        public ?string $foreignKey = null,
        public ?string $pivotTable = null,
        public ?string $pivotForeignKey = null,
    ) {}
}
