<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Dto\Constants\ContentType;
use MaliBoot\Dto\Constants\RequestParameterLocation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DataTransferObject extends AbstractAnnotation
{
    public function __construct(
        public string $name = '',
        public string $desc = '',
        public string $type = '',
        public string $in = RequestParameterLocation::BODY,
        public string $contentType = ContentType::WWW_FORM_URLENCODED
    ) {
    }
}
