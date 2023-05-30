<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode;

use Hyperf\Di\Exception\NotFoundException;
use MaliBoot\ErrorCode\Annotation\Message;
use MaliBoot\ErrorCode\Annotation\StatusCode;

class AnnotationReader
{
    public function getAnnotations(array $classConstants)
    {
        $result = [];
        /** @var \ReflectionClassConstant $classConstant */
        foreach ($classConstants as $classConstant) {
            $code = $classConstant->getValue();
            if (is_int($code) || is_string($code)) {
                $result[$code] = $this->getAttributes($classConstant);
            }
        }

        return $result;
    }

    public function getAttributes(\Reflector $reflection): array
    {
        $result = [];
        if (! method_exists($reflection, 'getAttributes')) {
            return $result;
        }

        /**
         * @var \ReflectionAttribute $attribute
         */
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            if (! class_exists($attribute->getName())) {
                $className = $constantName = '';
                if ($reflection instanceof \ReflectionClassConstant) {
                    $className = $reflection->getDeclaringClass()->getName();
                    $constantName = $reflection->getName();
                }
                $message = sprintf(
                    "No attribute class found for '%s' in %s",
                    $attribute->getName(),
                    $className
                );
                if ($constantName) {
                    $message .= sprintf('->%s() constant', $constantName);
                }
                throw new NotFoundException($message);
            }
            $instance = $attribute->newInstance();
            if ($instance instanceof StatusCode) {
                $result['statusCode'] = $attribute->getArguments()[0];
            } elseif ($instance instanceof Message) {
                $result['message'] = $attribute->getArguments()[0];
            }
        }
        return $result;
    }
}
