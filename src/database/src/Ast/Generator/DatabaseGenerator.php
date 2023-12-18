<?php

declare(strict_types=1);

namespace MaliBoot\Database\Ast\Generator;

use Hyperf\Stringable\Str;
use MaliBoot\Database\Contract\BelongsToAnnotationInterface;
use MaliBoot\Database\Contract\BelongsToManyAnnotationInterface;
use MaliBoot\Database\Contract\CastAnnotationInterface;
use MaliBoot\Database\Contract\DBAnnotationInterface;
use MaliBoot\Database\Contract\HasManyAnnotationInterface;
use MaliBoot\Database\Contract\HasOneAnnotationInterface;
use MaliBoot\Database\Contract\PrimaryKeyAnnotationInterface;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\Generator\DelegateGenerator;
use MaliBoot\Lombok\Contract\FieldNameOfAnnotationInterface;
use ReflectionAttribute;
use ReflectionProperty;

#[LombokGenerator]
class DatabaseGenerator extends DelegateGenerator
{
    protected function getClassMemberName(): string
    {
        return '_database';
    }

    protected function getAnnotationInterface(): string
    {
        return DBAnnotationInterface::class;
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
        $concernFields = [];
        $primaryKey = [];
        $concernMethods = '';
        foreach ($this->reflectionClass->getProperties() as $reflectionProperty) {
            $ofMapName = $this->getAttributeFnVal($reflectionProperty, FieldNameOfAnnotationInterface::class, 'getOfFieldName');
            $originName = $reflectionProperty->getName();
            $fieldName = $ofMapName ?: Str::snake($originName);

            // 模型关联
            $concernPrintf = $this->getConcernPrintf($reflectionProperty);
            $isConcernField = false;
            if (! empty($concernPrintf)) {
                $isConcernField = true;
                $fieldName = $originName;
                $concernFields[$fieldName] = $concernPrintf[1];
                $concernMethods .= $concernPrintf[0];
            }
            if (! $isConcernField) {
                // fillable填充
                $fillable[] = $fieldName;
                // cast
                $casts[$fieldName] = $this->getAttributeFnVal($reflectionProperty, CastAnnotationInterface::class, 'getCast') ?? $castsAttrs;
            }
            // 主键
            if (empty($primaryKey)) {
                $primaryKeyArgs = $this->getAttributeFnValues($reflectionProperty, PrimaryKeyAnnotationInterface::class, ['getPrimaryKeyName', 'getPrimaryKeyType']);
                $primaryKeyArgs['getPrimaryKeyName'] && $primaryKey[0] = $primaryKeyArgs['getPrimaryKeyName'];
                $primaryKeyArgs['getPrimaryKeyType'] && $primaryKey[1] = $primaryKeyArgs['getPrimaryKeyType'];
            }
        }
        $fillableStr = var_export($fillable, true);
        $castsStr = var_export($casts, true);
        $concernFieldsStr = var_export($concernFields, true);
        empty($primaryKey) && $primaryKey = ['id', 'int'];
        [$primaryKeyName, $primaryKeyType] = $primaryKey;
        $withFieldsStr = var_export(array_keys($concernFields), true);

        return <<<CODE
protected ?string \$table = '{$table}';
protected ?string \$connection = '{$connect}';
protected array \$fillable = {$fillableStr};
protected array \$casts = {$castsStr};
public array \$concerns = {$concernFieldsStr};
protected string \$primaryKey = '{$primaryKeyName}';
protected string \$keyType = '{$primaryKeyType}';
public array \$withConcerns = {$withFieldsStr};
use {$uses};

{$concernMethods}
CODE;
    }

    protected function getConcernPrintf(ReflectionProperty $reflectionProperty): array
    {
        $fieldName = $reflectionProperty->getName();
        $relatedModel = $this->getTypeFirstPClass($this->getPropertyType($reflectionProperty));
        $nullPrintf = fn ($data) => $data ? "'{$data}'" : 'null';
        $codePrintf = fn ($concernType, $concernArgsPrintf) => <<<CONCERNS
public function {$fieldName}()
{
    return \$this->{$concernType}({$concernArgsPrintf});
}
CONCERNS;

        // hasOne
        $hasOne = $reflectionProperty->getAttributes(HasOneAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! empty($hasOne) && $relatedModel) {
            /** @var HasOneAnnotationInterface $concernIns */
            $concernIns = $hasOne[0]->newInstance();
            $foreignKey = $concernIns->hasOneForeignKey();
            $concernArgsPrintf = sprintf(
                '"%s", %s, %s',
                $relatedModel,
                $nullPrintf($foreignKey),
                $nullPrintf($concernIns->hasOneLocalKey()),
            );
            return [$codePrintf('hasOne', $concernArgsPrintf), $relatedModel];
        }

        // hasMany
        $hasMany = $reflectionProperty->getAttributes(HasManyAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! empty($hasMany)) {
            /** @var HasManyAnnotationInterface $concernIns */
            $concernIns = $hasMany[0]->newInstance();

            $foreignKey = $concernIns->hasManyForeignKey();
            $relatedModel = $concernIns->hasManyRelated();
            $concernArgsPrintf = sprintf(
                '%s, %s, %s',
                $nullPrintf($relatedModel),
                $nullPrintf($foreignKey),
                $nullPrintf($concernIns->hasManyLocalKey()),
            );
            return [$codePrintf('hasMany', $concernArgsPrintf), $relatedModel];
        }

        // belongsTo
        $belongsTo = $reflectionProperty->getAttributes(BelongsToAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! empty($belongsTo) && $relatedModel) {
            /** @var BelongsToAnnotationInterface $concernIns */
            $concernIns = $belongsTo[0]->newInstance();
            $foreignKey = $concernIns->belongsToForeignKey();
            $concernArgsPrintf = sprintf(
                '"%s", %s, %s, %s',
                $relatedModel,
                $nullPrintf($foreignKey),
                $nullPrintf($concernIns->belongsToOwnerKey()),
                $nullPrintf($concernIns->belongsToRelation()),
            );
            return [$codePrintf('belongsTo', $concernArgsPrintf), $relatedModel];
        }

        // belongsToMany
        $belongsToMany = $reflectionProperty->getAttributes(BelongsToManyAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! empty($belongsToMany)) {
            /** @var BelongsToManyAnnotationInterface $concernIns */
            $concernIns = $belongsToMany[0]->newInstance();
            $foreignKey = $concernIns->belongsToManyForeignPivotKey();
            $relatedModel = $concernIns->belongsToManyRelated();
            $concernArgsPrintf = sprintf(
                '%s, %s, %s, %s, %s, %s',
                $nullPrintf($relatedModel),
                $nullPrintf($concernIns->belongsToManyTable()),
                $nullPrintf($foreignKey),
                $nullPrintf($concernIns->belongsToManyRelatedPivotKey()),
                $nullPrintf($concernIns->belongsToManyParentKey()),
                $nullPrintf($concernIns->belongsToManyRelation()),
            );
            return [$codePrintf('belongsToMany', $concernArgsPrintf), $relatedModel];
        }

        return [];
    }

    protected function getTable(DBAnnotationInterface $attribute): string
    {
        $table = $attribute->getTable();
        if ($table !== null) {
            return $table;
        }

        $className = $this->reflectionClass->getName();
        $className = \Hyperf\Collection\last(explode('\\', $className));
        $classNameLen = strlen($className);
        if ($classNameLen > 2 && $className[$classNameLen - 2] === 'D' && $className[$classNameLen - 1] === 'O') {
            $className = substr($className, 0, $classNameLen - 2);
        }
        return Str::snake($className);
    }

    protected function getUses(DBAnnotationInterface $attribute): string
    {
        $uses = ['\Hyperf\Database\Model\Concerns\CamelCase'];
        if ($attribute->softDeletes()) {
            $uses[] = '\Hyperf\Database\Model\SoftDeletes';
        }

        return implode(',', $uses);
    }

    protected function getDelegateClassName(): string
    {
        return '\MaliBoot\Database\AbstractModelDelegate';
    }

    private function getMyAttribute(): DBAnnotationInterface
    {
        /** @var ReflectionAttribute $attribute */
        $reflectionAttribute = $this->reflectionClass->getAttributes(DBAnnotationInterface::class, ReflectionAttribute::IS_INSTANCEOF)[0];
        /* @var DBAnnotationInterface $attribute */
        return $reflectionAttribute->newInstance();
    }
}
