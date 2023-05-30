<?php

declare(strict_types=1);

namespace MaliBoot\Utils;

use MaliBoot\Dto\PageVO;

class Convertor
{
    /**
     * 将当前对象转换为实体对象
     *
     * @param object $source 当前对象
     * @param object $entity 实体对象
     * @return mixed
     */
    public static function toEntity(object $source, object $entity)
    {
        return static::copyProperty($source, $entity);
    }

    /**
     * 将当前对象转换为实体对象
     *
     * @param array $sourceList 当前对象
     * @param object $entity 实体对象
     */
    public static function toListEntity(array $sourceList, object $entity): array
    {
        if (empty($sourceList)) {
            return [];
        }

        foreach ($sourceList as $source) {
            $entityList[] = $source->toEntity();
        }

        return $entityList;
    }

    /**
     * 将当前对象转换为DO对象
     *
     * @param object $source 当前对象
     * @param object $DO 持久化对象
     * @return mixed
     */
    public static function toDO(object $source, object $DO)
    {
        return static::copyProperty($source, $DO);
    }

    /**
     * 将当前对象转换为ViewObject对象
     *
     * @param object $source 当前对象
     * @param object $viewObject 视图对象
     * @return mixed
     */
    public static function toVO(object $source, object $viewObject)
    {
        return static::copyProperty($source, $viewObject);
    }

    /**
     * 将当前对象转换为ViewObject对象
     *
     * @param object $sourcePage 当前对象
     * @param string $vo
     */
    public static function toPageVO(object $sourcePage, string $VO): PageVO
    {
        if ($sourcePage->isEmpty()) {
            return new PageVO();
        }

        $VOList = [];
        foreach ($sourcePage->getItems() as $source) {
            $VOList[] = $VO::fromDO($source);
        }

        $pageVO = new PageVO($VOList);
        $pageVO->setPageSize($sourcePage->getPageSize());
        $pageVO->setTotalCount($sourcePage->getTotalCount());
        $pageVO->setPageIndex($sourcePage->getPageIndex());
        return $pageVO;
    }

    /**
     * 从源对象将属于复制到目标对象
     *
     * @return object
     */
    protected static function copyProperty(object $source, object $target)
    {
        if ($source === null) {
            return null;
        }

        $targetTmp = clone $target;

        if (method_exists($source, 'copyProperty')) {
            return $source->copyProperty($target);
        }

        foreach (get_object_vars($source) as $name => $value) {
            if (method_exists($targetTmp, 'get' . ucwords($name))) {
                $methodName = 'set' . ucwords($name);
                $targetTmp->{$methodName}($value);
            }
        }

        return $targetTmp;
    }
}
