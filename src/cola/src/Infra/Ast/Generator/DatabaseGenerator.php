<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra\Ast\Generator;

use Hyperf\Stringable\Str;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\Generator\DelegateGenerator;
use ReflectionAttribute;

#[LombokGenerator]
class DatabaseGenerator extends DelegateGenerator
{
    protected function getClassMemberName(): string
    {
        return '_database';
    }

    protected function getAnnotationInterface(): string
    {
        return DatabaseAnnotationInterface::class;
    }

    protected function getDelegateClassStmts(): string
    {
        $attribute = $this->getMyAttribute();
        $castsAttrs = $attribute->getCastsAttributes();
        $castsAttrs[0] !== '\\' && $castsAttrs = '\\' . $castsAttrs;
        $table = $this->getTable($attribute);
        $connect = $attribute->getConnection();
        $uses = $this->getUses($attribute);
        $fillable = [];
        $casts = [];
        foreach ($this->reflectionClass->getProperties() as $reflectionProperty) {
            $fieldName = Str::snake($reflectionProperty->getName());
            $fillable[] = $fieldName;
            $casts[$fieldName] = $castsAttrs;
        }
        $fillableStr = var_export($fillable, true);
        $castsStr = var_export($casts, true);

        return <<<CODE
protected ?string \$table = '{$table}';
protected ?string \$connection = '{$connect}';
protected array \$fillable = {$fillableStr};
protected array \$casts = {$castsStr};
use {$uses};
CODE;
    }

    protected function getTable(DatabaseAnnotationInterface $attribute): string
    {
        $table = $attribute->getTable();
        if ($table !== null) {
            return $table;
        }

        $className = $this->reflectionClass->getName();
        $className = \Hyperf\Collection\last(explode('\\', $className));
        $className = trim($className, 'DO');
        return Str::snake($className);
    }

    protected function getUses(DatabaseAnnotationInterface $attribute): string
    {
        $uses = ['\Hyperf\Database\Model\Concerns\CamelCase'];
        if ($attribute->useSoftDeletes()) {
            $uses[] = '\Hyperf\Database\Model\SoftDeletes';
        }

        return implode(',', $uses);
    }

    protected function getDelegateClassName(): string
    {
        return '\MaliBoot\Cola\Infra\AbstractModelDelegate';
    }

    private function getMyAttribute(): DatabaseAnnotationInterface
    {
        /** @var ReflectionAttribute $attribute */
        $reflectionAttribute = $this->reflectionClass->getAttributes(DatabaseAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF)[0];
        /* @var DatabaseAnnotationInterface $attribute */
        return $reflectionAttribute->newInstance();
    }
}
