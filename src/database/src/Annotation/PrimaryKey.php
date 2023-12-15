<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Database\Contract\PrimaryKeyAnnotationInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey implements PrimaryKeyAnnotationInterface
{
    /**
     * 主键.
     * @param null|string $name 主键名称，默认id
     * @param null|string $type 主键类型，默认int
     */
    public function __construct(
        public ?string $name = null,
        public ?string $type = null,
    ) {}

    public function getPrimaryKeyName(): ?string
    {
        return $this->name;
    }

    public function getPrimaryKeyType(): ?string
    {
        return $this->type;
    }
}
