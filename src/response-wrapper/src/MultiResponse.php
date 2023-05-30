<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

use MaliBoot\Utils\Collection;

/**
 * Response with batch record to return,
 * usually use in conditional query.
 *
 * @template T
 */
class MultiResponse extends Response
{
    /**
     * @var Collection<T>
     */
    private Collection $data;

    /**
     * @return Collection<T>
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param Collection<T> $data
     */
    public function setData(Collection $data): self
    {
        $this->data = $data;
        return $this;
    }

    public static function buildSuccess(): MultiResponse
    {
        $response = new MultiResponse();
        $response->setSuccess(true)
            ->setData(new Collection());
        return $response;
    }

    public static function buildFailure(int $errCode, string $errMessage): MultiResponse
    {
        $response = new MultiResponse();
        $response->setSuccess(false)
            ->setErrCode($errCode)
            ->setErrMessage($errMessage)
            ->setData(new Collection());
        return $response;
    }

    /**
     * @param Collection<T> $data
     * @return MultiResponse<T>
     */
    public static function of(Collection $data): MultiResponse
    {
        $response = new MultiResponse();
        $response->setSuccess(true)
            ->setData($data);

        return $response;
    }
}
