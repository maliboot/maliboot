<?php

declare(strict_types=1);

namespace MaliBoot\Database;

use Hyperf\Contract\CastsAttributes;

/**
 * 数据对象的模型字段的类型映射，按需要进行继承改写.
 * @document https://hyperf.wiki/3.0/#/zh-cn/db/mutators?id=字段类型转换
 */
class DODatabaseCastsAttributes implements CastsAttributes
{
    /**
     * 应进行类型转换的字段 ...
     * @var array ...
     */
    protected array $casts = [
    ];

    /**
     * 将取出的数据进行转换.
     * @param AbstractModelDelegate $model 模型
     * @param string $key 字段名称
     * @param mixed $value 字段值
     * @param array $attributes 模型其它所有字段[key=>value]
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * 转换成将要进行存储的值
     * @param AbstractModelDelegate $model 模型
     * @param string $key 字段名称
     * @param mixed $value 字段值
     * @param array $attributes 模型其它所有字段[key=>value]
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
