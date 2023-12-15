<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Database\Contract\DBAnnotationInterface;
use MaliBoot\Database\DODatabaseCastsAttributes;
use MaliBoot\Database\DODatabaseFieldDelegate;
use MaliBoot\Lombok\Log\LoggerAnnotationTrait;

#[Attribute(Attribute::TARGET_CLASS)]
class DB extends AbstractAnnotation implements DBAnnotationInterface
{
    use LoggerAnnotationTrait;

    /**
     * <h3>数据库ORM委托.</h3>
     * <p>-注：本类默认将所有逻辑，全权委托于 <a href='psi_element://\MaliBoot\Database\AbstractModelDelegate'>AbstractModelDelegate</a> 处理</p>.
     * @param null|string $table 表名称
     * @param string $connection 数据库连接
     * @param bool $softDeletes 是否使用软删除
     * @param string $castsAttributes 类名称。功能：自定义字段的类型映射。需要实现<a href='psi_element://\Hyperf\Contract\CastsAttributes'>CastsAttributes</a>
     */
    public function __construct(
        public ?string $table = null,
        public string $connection = 'default',
        public bool $softDeletes = false,
        public string $castsAttributes = DODatabaseCastsAttributes::class,
    ) {}

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function softDeletes(): bool
    {
        return $this->softDeletes;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function getCastsAttributes(): string
    {
        return $this->castsAttributes;
    }

    public function getterDelegate(): ?string
    {
        return DODatabaseFieldDelegate::class;
    }

    public function setterDelegate(): ?string
    {
        return DODatabaseFieldDelegate::class;
    }
}
