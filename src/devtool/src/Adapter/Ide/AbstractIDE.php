<?php

namespace MaliBoot\Devtool\Adapter\Ide;

abstract class AbstractIDE
{
    public function response(string $result = '', int $code = 200): array
    {
        return [
            'code' => $code,
            'result' => $result
        ];
    }
}