<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use MaliBoot\Lombok\Contract\GetterAnnotationInterface;
use MaliBoot\Lombok\Contract\SetterAnnotationInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DataObject extends AbstractAnnotation implements GetterAnnotationInterface, SetterAnnotationInterface
{
    public function __construct(
        public string $domain = '',
        public string $name = '',
        public string $desc = '',
        public string $table = '',
        public string $connection = 'default'
    ) {
    }
}
