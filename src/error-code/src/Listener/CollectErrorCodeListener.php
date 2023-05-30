<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode\Listener;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use MaliBoot\ErrorCode\Annotation\ErrorCode;
use MaliBoot\ErrorCode\AnnotationReader;
use MaliBoot\ErrorCode\ErrorCodeCollector;

class CollectErrorCodeListener implements ListenerInterface
{
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event): void
    {
        $errorCodeClasses = AnnotationCollector::getClassesByAnnotation(ErrorCode::class);
        foreach ($errorCodeClasses as $className => $annotation) {
            $reader = new AnnotationReader();

            $ref = new \ReflectionClass($className);
            $classConstants = $ref->getReflectionConstants();
            $errorCodes = $reader->getAnnotations($classConstants);

            foreach ($errorCodes as $code => $value) {
                ErrorCodeCollector::setValue($code, $value);
            }
        }
    }
}
