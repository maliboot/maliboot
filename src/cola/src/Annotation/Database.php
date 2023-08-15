<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Cola\Infra\Ast\Generator\DatabaseAnnotationInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class Database extends AbstractAnnotation implements DatabaseAnnotationInterface
{
    public function __construct(
        public ?string $table = null,
        public bool $useSoftDeletes = false,
        public string $connection = 'default',
    ) {
    }

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function useSoftDeletes(): bool
    {
        return $this->useSoftDeletes;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }
}
