<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra\Ast\Generator;

use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class OfEntityGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return 'ofEntity';
    }

    protected function getAnnotationInterface(): string
    {
        return OfEntityAnnotationInterface::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class Template {
    public static function ofEntity(object $entity): static
    {
        $ins = new static();
        if (! method_exists($entity, 'toArray') || ! method_exists($ins, 'ofData')) {
            return $ins;
        }
        return $ins->ofData($entity->toArray());
    }
}
CODE;
    }
}
