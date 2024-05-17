<?php

declare(strict_types=1);

namespace MaliBoot\Dto\Ast\Generator;

use MaliBoot\Dto\Contract\PageVOConvertorInterface;
use MaliBoot\Dto\PageVO;
use MaliBoot\Lombok\Annotation\LombokGenerator;
use MaliBoot\Lombok\Ast\AbstractClassVisitor;

#[LombokGenerator]
class PageVOConvertorGenerator extends AbstractClassVisitor
{
    protected function getClassMemberName(): string
    {
        return 'pageVO';
    }

    protected function getAnnotationInterface(): string
    {
        return PageVOConvertorInterface::class;
    }

    protected function enable(): bool
    {
        if (! parent::enable()) {
            return false;
        }

        return $this->reflectionClass->getName() != PageVO::class;
    }

    protected function getClassCodeSnippet(): string
    {
        return <<<'CODE'
<?php
class ClientObject {
    public static function pageVO(\MaliBoot\Dto\PageVO $pageVO): \MaliBoot\Dto\PageVO
    {
        $result = clone $pageVO;
        $newItems = \MaliBoot\Utils\Collection::make();
        $result->setItemType(static::class);
        foreach ($result->getItems() as $item) {
            if (\MaliBoot\Utils\ObjectUtil::isVO($item)) {
                $newItems->push($item);
                continue;
            }
            if (is_array($item)) {
                $newItems->push(static::of($item));
                continue;
            }
            if (is_object($item) && method_exists($item, 'toArray')) {
                $newItems->push(static::of($item->toArray()));
                continue;
            }
            $newItems->push($item);
        }
        $result->setItems($newItems);
        return $result;
    }
}
CODE;
    }
}
