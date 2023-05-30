<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

use MaliBoot\Utils\Contract\Arrayable;
use MaliBoot\Utils\Traits\SetPropertiesTrait;

abstract class Response
{
    use SetPropertiesTrait;

    protected bool $success = true;

    protected int $errCode = 0;

    protected string $errCodeKey = 'code';

    protected string $errMessage = 'success';

    protected string $errMessageKey = 'msg';

    protected string $dataKey = 'data';

    protected string $debugKey = 'debug';

    protected bool $debug = false;

    protected string $debugTraceKey = 'trace';

    protected string|array $debugTrace = '';

    protected string $debugSqlKey = 'sql';

    protected array $debugSql = [];

    public function __toString()
    {
        $str = json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        return (string) $str;
    }

    public function toArray(): array
    {
        $result = [
            $this->errCodeKey => $this->getErrCode(),
            $this->errMessageKey => $this->getErrMessage(),
            $this->dataKey => $this->dataToArrayOrObject($this->getData()),
        ];
        if ($this->debug) {
            $result[$this->debugKey] = [
                $this->debugTraceKey => $this->getDebugTrace(),
                $this->debugSqlKey => $this->getDebugSql(),
            ];
        }

        return $result;
    }

    public function getErrCode(): int
    {
        return $this->errCode;
    }

    public function setErrCode(int $errCode = 0): self
    {
        $this->errCode = $errCode;
        return $this;
    }

    public function getErrMessage(): string
    {
        return $this->errMessage;
    }

    public function setErrMessage(string $errMessage = 'ok'): self
    {
        $this->errMessage = $errMessage;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    abstract public function getData();

    public function getDebugTrace(): string|array
    {
        return $this->debugTrace;
    }

    public function setDebugTrace(string|array $debugTrace): static
    {
        $this->debugTrace = $debugTrace;
        return $this;
    }

    public function getDebugSql(): array
    {
        return $this->debugSql;
    }

    public function setDebugSql(array $debugSql): static
    {
        $this->debugSql = $debugSql;
        return $this;
    }

    public function setErrMessageKey(string $errMessageKey): void
    {
        $this->errMessageKey = $errMessageKey;
    }

    public function setErrCodeKey(string $errCodeKey): void
    {
        $this->errCodeKey = $errCodeKey;
    }

    public function setDataKey(string $dataKey): void
    {
        $this->dataKey = $dataKey;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): static
    {
        $this->debug = $debug;
        return $this;
    }

    protected function dataToArrayOrObject($data)
    {
        if ($this->hasToArray($data)) {
            $data = $data->toArray();
        }

        if (! $this->isSuccess()) {
            $data = new \stdClass();
        }

        return $data;
    }

    protected function hasToArray($data): bool
    {
        return $data instanceof \Hyperf\Contract\Arrayable
            || $data instanceof Arrayable
            || method_exists($data, 'toArray');
    }
}
