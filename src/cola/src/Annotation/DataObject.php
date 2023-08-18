<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Cola\Infra\Ast\Generator\OfEntityAnnotationInterface;
use MaliBoot\Cola\Infra\Ast\Generator\ToEntityAnnotationInterface;
use MaliBoot\Dto\Contract\StructureObjectAnnotationInterface;
use MaliBoot\Lombok\Contract\GetterAnnotationInterface;
use MaliBoot\Lombok\Contract\SetterAnnotationInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DataObject extends AbstractAnnotation implements StructureObjectAnnotationInterface, ToEntityAnnotationInterface, OfEntityAnnotationInterface
{
    /**
     * @param ?string $getterSetterDelegate GetterSetter委托类<div><p>默认为null，无委托</p><p>委托类需要实现<a href='psi_element://\MaliBoot\Lombok\Contract\GetterSetterDelegateInterface'>GetterSetterDelegateInterface</a></p></div>
     */
    public function __construct(
        public string $domain = '',
        public string $name = '',
        public string $desc = '',
        public string $table = '',
        public string $connection = 'default',
        public ?string $getterSetterDelegate = null,
    ) {
    }

    public function getterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }

    public function setterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }
}
