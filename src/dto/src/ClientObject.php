<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

/**
 * 客户端通信的对象，客户端可以是视图层或其他RPC消费者.
 * @deprecated ...
 */
abstract class ClientObject extends AbstractDTO
{
    public static function ofDO($do): ?static
    {
        if (is_null($do)) {
            return null;
        }

        $vo = new static();
        $vo->setProperties($do->toArray());
        return $vo;
    }
}
