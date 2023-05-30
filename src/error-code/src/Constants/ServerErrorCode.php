<?php

declare(strict_types=1);

namespace MaliBoot\ErrorCode\Constants;

use MaliBoot\ErrorCode\Annotation\ErrorCode;
use MaliBoot\ErrorCode\Annotation\Message;
use MaliBoot\ErrorCode\Annotation\StatusCode;

#[ErrorCode]
class ServerErrorCode extends AbstractErrorCode
{
    #[StatusCode(500)]
    #[Message('服务器异常')]
    public const SERVER_ERROR = 100001;

    #[StatusCode(500)]
    #[Message('参数错误')]
    public const INVALID_PARAMS = 100002;

    #[StatusCode(500)]
    #[Message('查询条件参数错误')]
    public const WHERE_INVALID_PARAMS = 100003;
}
