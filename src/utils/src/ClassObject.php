<?php

declare(strict_types=1);

namespace MaliBoot\Utils;

class ClassObject
{
    /**
     * 将当前对象属性复制到目标对象
     *
     * @return object
     */
    protected function copyProperties(object $src, object $dest)
    {
        $newDest = clone $dest;
        foreach ($src->all() as $name => $value) {
            // TODO 需要解决深拷贝问题
            $setterMethodName = setter($name);
            $newDest->{$setterMethodName}($value);
        }

        return $newDest;
    }
}
