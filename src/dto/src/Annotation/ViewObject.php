<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Contract\BaseDTOAnnotationInterface;
use MaliBoot\Dto\Contract\OfDOAnnotationInterface;
use MaliBoot\Dto\Contract\PageVOConvertorInterface;
use MaliBoot\Lombok\Log\LoggerAnnotationTrait;

#[Attribute(Attribute::TARGET_CLASS)]
class ViewObject extends AbstractAnnotation implements BaseDTOAnnotationInterface, OfDOAnnotationInterface, PageVOConvertorInterface
{
    use LoggerAnnotationTrait;

    /**
     * @param ?string $getterSetterDelegate GetterSetter委托类<div><p>默认为null，无委托</p><p>委托类需要实现<a href='psi_element://\MaliBoot\Lombok\Contract\GetterSetterDelegateInterface'>GetterSetterDelegateInterface</a></p></div>
     */
    public function __construct(public string $name = '', public string $desc = '', public ?string $getterSetterDelegate = null) {}

    public function getterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }

    public function setterDelegate(): ?string
    {
        return $this->getterSetterDelegate;
    }
}
