<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Ast\Generator;

use MaliBoot\Dto\Contract\OfDOAnnotationInterface;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class OfDOGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return 'ofDO';
    }

    protected function getAnnotationInterface(): string
    {
        return OfDOAnnotationInterface::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class ClientObject {
    public static function ofDO(object $do): ?static
    {
        if (! method_exists($do, 'toArray')) {
            return null;
        }

        $vo = new static();
        if (! method_exists($vo, 'ofData')) {
            return null;
        }
        $doData = $do->toArray();
        (isset($doData[0]) && is_array($doData[0])) && $doData = $doData[0];
        return $vo->ofData($doData);
    }
}
CODE;
    }
}
