<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

use MaliBoot\Cola\Domain\AggregateRootInterface;
use MaliBoot\Cola\Domain\EntityInterface;

interface DataObjectInterface
{
    /**
     * 将实体转换为 DO.
     *
     * @return DataObjectInterface
     */
    public static function ofEntity(AggregateRootInterface $entity);

    /**
     * 将 DO 转换为实体.
     *
     * @param null|string $entityFQN 指定实体FQN
     * @return ?EntityInterface
     */
    public function toEntity(?string $entityFQN = null);

    /**
     * 设置属性.
     */
    public function setProperties(array $args): void;
}
