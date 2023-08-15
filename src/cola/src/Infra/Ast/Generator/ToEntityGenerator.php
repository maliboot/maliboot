<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra\Ast\Generator;

use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class ToEntityGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return 'toEntity';
    }

    protected function getAnnotationInterface(): string
    {
        return ToEntityAnnotationInterface::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class Template {
    public function toEntity(?string $entityFQN = null)
    {
        if ($entityFQN == null) {
            if (method_exists($this, 'getEntityFQN')) {
                $entityFQN = $this->getEntityFQN();
            } else {
                $dataObject = get_class($this);
                $dataObjectArr = explode('\\', $dataObject);
                $dataObjectClassName = end($dataObjectArr);
                $dataObjectClassName = rtrim($dataObjectClassName, 'DO');
        
                $entityFQN = str_replace(['Infra\DataObject', 'DO'], ['Domain\Model\\' . $dataObjectClassName, ''], $dataObject);
            }
        }
        if ($entityFQN === null || ! class_exists($entityFQN)) {
            return null;
        }
        return call_user_func([$entityFQN, 'of'], $this->attributesToArray());
    }
}
CODE;
    }
}
