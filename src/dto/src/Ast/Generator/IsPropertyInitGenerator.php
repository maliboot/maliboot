<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Ast\Generator;

use MaliBoot\Dto\Contract\IsPropertyInitAnnotationInterface;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class IsPropertyInitGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return 'isPropertyInitialized';
    }

    protected function getAnnotationInterface(): string
    {
        return IsPropertyInitAnnotationInterface::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class Context {
    protected function isPropertyInitialized(string $property): bool
    {
        $reflectProperty = new \ReflectionProperty($this, $property);
        return $reflectProperty->isInitialized($this);
    }
}
CODE;
    }
}
