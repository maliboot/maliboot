<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Constants\ContentType;
use MaliBoot\Dto\Constants\RequestParameterLocation;
use MaliBoot\Dto\Contract\BaseDTOAnnotationInterface;
use MaliBoot\Dto\Contract\QueryDTOAnnotationInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class DataTransferObject extends AbstractAnnotation implements BaseDTOAnnotationInterface, QueryDTOAnnotationInterface
{
    public function __construct(
        public string $name = '',
        public string $desc = '',
        public string $type = '',
        public string $in = RequestParameterLocation::BODY,
        public string $contentType = ContentType::WWW_FORM_URLENCODED
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
