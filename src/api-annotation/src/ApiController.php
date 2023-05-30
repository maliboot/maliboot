<?php

declare(strict_types=1);

namespace MaliBoot\ApiAnnotation;

use Attribute;
use Hyperf\HttpServer\Annotation\Controller;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiController extends Controller
{
}
