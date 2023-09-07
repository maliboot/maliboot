<?php

namespace MaliBoot\Devtool\Ide;

abstract class AbstractHttp
{
    public function response(string $result = '', int $code = 200): array
    {
        return [
            'code' => $code,
            'result' => $result
        ];
    }
}