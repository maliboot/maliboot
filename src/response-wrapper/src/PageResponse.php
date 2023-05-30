<?php

declare(strict_types=1);

namespace MaliBoot\ResponseWrapper;

use MaliBoot\Dto\PageVO;
use MaliBoot\Utils\Collection;

/**
 * @template T
 */
class PageResponse extends Response
{
    /**
     * @var int 总数
     */
    private int $totalCount = 0;

    /**
     * @var int 每页数量
     */
    private int $pageSize = 1;

    /**
     * @var int 当前页索引
     */
    private int $pageIndex = 1;

    /**
     * @var Collection<T>
     */
    private Collection $data;

    public static function buildSuccess(): PageResponse
    {
        $response = new PageResponse();
        $response->setSuccess(true)
            ->setData(new Collection());
        return $response;
    }

    public static function buildFailure(int $errCode, string $errMessage): PageResponse
    {
        $response = new PageResponse();
        $response->setSuccess(false)
            ->setErrCode($errCode)
            ->setErrMessage($errMessage)
            ->setData(new Collection());
        return $response;
    }

    /**
     * @return PageResponse<T>
     */
    public static function of(PageVO $pageVO): PageResponse
    {
        $response = new PageResponse();
        $response->setSuccess(true)
            ->setData($pageVO->getItems())
            ->setTotalCount($pageVO->getTotalCount())
            ->setPageSize($pageVO->getPageSize())
            ->setPageIndex($pageVO->getPageIndex());

        return $response;
    }

    public function toArray(): array
    {
        $result = [
            $this->errCodeKey => $this->getErrCode(),
            $this->errMessageKey => $this->getErrMessage(),
            $this->dataKey => [
                'totalCount' => $this->getTotalCount(),
                'pageSize' => $this->getPageSize(),
                'pageIndex' => $this->getPageIndex(),
                'items' => $this->dataToArrayOrObject($this->getData()),
            ],
        ];

        if ($this->debug) {
            $result[$this->debugKey] = [
                $this->debugTraceKey => $this->getDebugTrace(),
                $this->debugSqlKey => $this->getDebugSql(),
            ];
        }

        return $result;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): self
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    public function getPageSize(): int
    {
        if ($this->pageSize < 1) {
            return 1;
        }
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): self
    {
        if ($pageSize < 1) {
            $pageSize = 1;
        }
        $this->pageSize = $pageSize;
        return $this;
    }

    public function getPageIndex(): int
    {
        if ($this->pageIndex < 1) {
            return 1;
        }

        return $this->pageIndex;
    }

    public function setPageIndex(int $pageIndex): self
    {
        if ($pageIndex < 1) {
            $pageIndex = 1;
        }
        $this->pageIndex = $pageIndex;
        return $this;
    }

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

    public function isEmpty()
    {
        return empty($this->getData());
    }
}
