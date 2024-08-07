<?php

declare(strict_types=1);

namespace MaliBoot\Lombok\Ast;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Aop\VisitorMetadata;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;

class LombokVisitor extends NodeVisitorAbstract
{
    public ?Class_ $class_ = null;

    public function __construct(protected VisitorMetadata $visitorMetadata) {}

    public function beforeTraverse(array $nodes)
    {
        foreach ($nodes as $namespace) {
            if ($namespace instanceof Node\Stmt\Declare_) {
                continue;
            }

            if (! $namespace instanceof Node\Stmt\Namespace_) {
                break;
            }

            foreach ($namespace->stmts as $class) {
                if ($class instanceof Node\Stmt\ClassLike) {
                    $this->visitorMetadata->classLike = get_class($class);
                }
                if ($class instanceof Class_) {
                    $this->class_ = $class;
                }
            }
        }
        return null;
    }

    public function leaveNode(Node $node)
    {
        switch ($node) {
            case $node instanceof Interface_:
                $this->visitInterface($node);
                break;
            case $node instanceof Class_:
                $this->visitClass($node);
                break;
            case $node instanceof ClassMethod:
                $this->visitClassMethod($node);
                break;
            case $node instanceof Property:
                $this->visitClassProperty($node);
                break;
        }
        return null;
    }

    private function visitClass(Class_ $class_): void
    {
        $classReflection = new ReflectionClass($this->visitorMetadata->className);
        $generatorAnnotationList = AnnotationCollector::getClassesByAnnotation(LombokGenerator::class);
        foreach ($generatorAnnotationList as $generatorClassName => $generatorAnnotation) {
            if (! is_subclass_of($generatorClassName, AbstractVisitor::class)) {
                continue;
            }

            // classAnnotation
            if (is_subclass_of($generatorClassName, AbstractClassVisitor::class)) {
                $generatorIns = new $generatorClassName($class_, $classReflection);
                $generatorIns->execute();
            }

            // classFieldAnnotation
            if (is_subclass_of($generatorClassName, AbstractClassFieldVisitor::class)) {
                foreach ($classReflection->getProperties() as $reflectionProperty) {
                    $generatorIns = new $generatorClassName($class_, $reflectionProperty);
                    $generatorIns->execute();
                }
            }
        }
    }

    private function visitInterface(Interface_ $interface_): void {}

    private function visitClassMethod(ClassMethod $method_): void {}

    private function visitClassProperty(Property $property_): void {}
}
