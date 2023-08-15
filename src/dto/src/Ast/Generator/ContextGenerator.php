<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Ast\Generator;

use MaliBoot\Dto\Contract\ContextAnnotationInterface;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class ContextGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return '_context';
    }

    protected function getAnnotationInterface(): string
    {
        return ContextAnnotationInterface::class;
    }

    protected function getClassMemberType(): string
    {
        return parent::PROPERTY;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class Context {
    private ?\MaliBoot\Dto\Context $_context = null;

    public function getUser(): ?\MaliBoot\Dto\UserContext
    {
        if (is_null($this->_context)) {
            return null;
        }

        return $this->_context->getUser();
    }

    /**
     * alias of getUser.
     */
    public function user(): ?\MaliBoot\Dto\UserContext
    {
        return $this->getUser();
    }

    public function setUser(?\MaliBoot\Dto\UserContext $user): static
    {
        if (is_null($this->_context)) {
            $this->_context = new \MaliBoot\Dto\Context();
        }

        $this->_context->setUser($user);

        return $this;
    }
}
CODE;
    }
}
