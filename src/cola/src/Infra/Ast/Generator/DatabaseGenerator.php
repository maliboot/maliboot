<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra\Ast\Generator;

use Hyperf\Stringable\Str;
use MaliBoot\Cola\Annotation\ORM;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\Generator\DelegateGenerator;
use MaliBoot\Utils\ObjectUtil;
use ReflectionAttribute;

#[LombokGenerator]
class DatabaseGenerator extends DelegateGenerator
{
    private array $concernFields = [];

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
        $concernFields = [];
        $concernMethods = '';
        foreach ($this->reflectionClass->getProperties() as $reflectionProperty) {
            $ormList = $reflectionProperty->getAttributes(ORM::class, ReflectionAttribute::IS_INSTANCEOF);
            $ormArgs = empty($ormList) ? [] : $ormList[0]->getArguments();
            $fieldName = Str::snake($reflectionProperty->getName());
            $isORMField = true;

            // 模型关联
            $relatedModel = $this->getTypeFirstPClass($this->getPropertyType($reflectionProperty));
            if ($relatedModel !== null && ObjectUtil::isDataObject($relatedModel) && ! empty($ormArgs['concern'])) {
                $isORMField = false;
                $concernArgsCode = match ($ormArgs['concern']) {
                    'hasOne', 'hasMany', 'belongsTo' => sprintf('"%s", "%s"', $relatedModel, $ormArgs['foreignKey'] ?? null),
                    'belongsToMany' => sprintf('"%s", "%s", "%s", "%s"', $relatedModel, $ormArgs['pivotTable'] ?? null, $ormArgs['foreignKey'] ?? null, $ormArgs['pivotForeignKey'] ?? null),
                    default => null,
                };
                $concernArgsCode && $concernFields[] = $fieldName;
                $concernArgsCode && $concernMethods .= <<<CONCERNS

public function {$fieldName}()
{
    return \$this->{$ormArgs['concern']}({$concernArgsCode});
}
CONCERNS;
            }
            // fillable填充
            $fillable[] = $fieldName;
            if ($isORMField) {
                // cast填充
                $casts[$fieldName] = $ormArgs['cast'] ?? $castsAttrs;
            }
        }
        $fillableStr = var_export($fillable, true);
        $castsStr = var_export($casts, true);
        $concernFieldsStr = var_export($concernFields, true);
        $this->concernFields = $concernFields;

        return <<<CODE
protected ?string \$table = '{$table}';
protected ?string \$connection = '{$connect}';
protected array \$fillable = {$fillableStr};
protected array \$casts = {$castsStr};
public array \$concernFields = {$concernFieldsStr};
use {$uses};

{$concernMethods}
CODE;
    }

    protected function getDelegateInsStmts(): string
    {
        $result = '';
        if (! empty($this->concernFields)) {
            $result .= sprintf('$delegateIns->with(%s);', var_export($this->concernFields, true));
        }
        return $result;
    }

    protected function getTable(DatabaseAnnotationInterface $attribute): string
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

    protected function getUses(DatabaseAnnotationInterface $attribute): string
    {
        $uses = ['\Hyperf\Database\Model\Concerns\CamelCase'];
        if ($attribute->softDeletes()) {
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

    private function getConcerns(): string
    {
        $result = '';
        foreach ($this->reflectionClass->getProperties() as $property) {
            $modelClazz = $this->getTypeFirstPClass($this->getPropertyType($property));
            if ($modelClazz === null) {
                continue;
            }
            if (! ObjectUtil::isDataObject($modelClazz)) {
                continue;
            }

            $orms = $property->getAttributes(ORM::class, ReflectionAttribute::IS_INSTANCEOF);
            if (empty($orm)) {
                continue;
            }
            $ormArgs = $orms[0]->getArguments();

            $result .= <<<CONCERNS

public function {$property->name}()
{
    return \$this->hasOne({$modelClazz}, 'user_id', 'id');
}
CONCERNS;
        }
        return $result;
    }
}
