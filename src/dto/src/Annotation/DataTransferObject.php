<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use JetBrains\PhpStorm\ExpectedValues;
use MaliBoot\Dto\Constants\ContentType;
use MaliBoot\Dto\Constants\RequestParameterLocation;
use MaliBoot\Dto\Contract\BaseDTOAnnotationInterface;
use MaliBoot\Dto\Contract\QueryDTOAnnotationInterface;
use MaliBoot\Lombok\Log\LoggerAnnotationTrait;

#[Attribute(Attribute::TARGET_CLASS)]
class DataTransferObject extends AbstractAnnotation implements BaseDTOAnnotationInterface, QueryDTOAnnotationInterface
{
    use LoggerAnnotationTrait;

    /**
     * @param ?string $getterSetterDelegate GetterSetter委托类<div><p>默认为null，无委托</p><p>委托类需要实现<a href='psi_element://\MaliBoot\Lombok\Contract\GetterSetterDelegateInterface'>GetterSetterDelegateInterface</a></p></div>
     */
    public function __construct(
        public string $name = '',
        public string $desc = '',
        #[ExpectedValues(['command', 'query', 'query-page'])] public string $type = '',
        public string $in = RequestParameterLocation::BODY,
        public string $contentType = ContentType::WWW_FORM_URLENCODED,
        public ?string $getterSetterDelegate = null,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
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
