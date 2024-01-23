<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Event extends DataTransferObject
{
}