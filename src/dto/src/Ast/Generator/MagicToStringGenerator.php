<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Ast\Generator;

use MaliBoot\Dto\Contract\MagicToStringAnnotationInterface;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class MagicToStringGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return '__toString';
    }

    protected function getAnnotationInterface(): string
    {
        return MagicToStringAnnotationInterface::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class Context {
    public function __toString(): string
    {
        $result = $this->toArray();
        if (empty($result)) {
            $result = new \stdClass();
        }

        return \Hyperf\Codec\Json::encode($result);
    }
}
CODE;
    }
}
