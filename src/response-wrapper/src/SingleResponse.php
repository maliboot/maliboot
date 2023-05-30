<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

class SingleResponse extends Response
{
    private object $data;

    public function getData(): object
    {
        return $this->data;
    }

    public function setData(object $data): static
    {
        $this->data = $data;
        return $this;
    }

    public static function buildSuccess(): SingleResponse
    {
        $response = new SingleResponse();
        $response->setSuccess(true)
            ->setData(new \stdClass());
        return $response;
    }

    public static function buildFailure(int $errCode, string $errMessage): SingleResponse
    {
        $response = new SingleResponse();
        $response->setSuccess(false)
            ->setErrCode($errCode)
            ->setErrMessage($errMessage)
            ->setData(new \stdClass());
        return $response;
    }

    public static function of(object $data): SingleResponse
    {
        $response = new SingleResponse();
        $response->setSuccess(true)
            ->setData($data);

        return $response;
    }
}
