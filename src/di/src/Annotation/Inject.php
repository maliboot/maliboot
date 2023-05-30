<?php

declare(strict_types=1);

namespace MaliBoot\Di\Annotation;

use Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Inject extends \Hyperf\Di\Annotation\Inject
{
}
