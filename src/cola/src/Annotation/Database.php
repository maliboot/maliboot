<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Attribute;
use MaliBoot\Cola\Infra\Ast\Generator\OfEntityAnnotationInterface;
use MaliBoot\Cola\Infra\Ast\Generator\ToEntityAnnotationInterface;
use MaliBoot\Database\Annotation\DB;

#[Attribute(Attribute::TARGET_CLASS)]
class Database extends DB implements ToEntityAnnotationInterface, OfEntityAnnotationInterface {}
