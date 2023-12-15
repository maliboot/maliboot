<?php

declare(strict_types=1);

namespace MaliBoot\Database\Annotation;

use Attribute;
use MaliBoot\Lombok\Annotation\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column extends Field {}
