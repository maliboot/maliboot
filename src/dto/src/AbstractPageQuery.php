<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\Field;

abstract class AbstractPageQuery extends AbstractQuery
{
    public const DEFAULT_PAGE_SIE = 10;

    #[Field(name: '每页数量', type: 'integer')]
    protected int $pageSize = self::DEFAULT_PAGE_SIE;

    #[Field(name: '当前页', type: 'integer')]
    protected int $pageIndex = 1;

    protected string|array $orderBy = '';

    protected string $groupBy;

    protected bool $needTotalCount = true;

    protected array $filters = [];

    protected array $columns = ['*'];

    public function getPageSize(): int
    {
        if ($this->pageSize < 1) {
            return self::DEFAULT_PAGE_SIE;
        }
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): self
    {
        if ($pageSize < 1) {
            $pageSize = self::DEFAULT_PAGE_SIE;
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

    public function getOrderBy(): string|array
    {
        return $this->orderBy;
    }

    public function setOrderBy(string|array $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getGroupBy(): string
    {
        return $this->groupBy;
    }

    public function setGroupBy(string $groupBy): self
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function isNeedTotalCount(): bool
    {
        return $this->needTotalCount;
    }

    public function setNeedTotalCount(bool $needTotalCount): self
    {
        $this->needTotalCount = $needTotalCount;
        return $this;
    }

    public function getOffset()
    {
        return ($this->getPageIndex() - 1) * $this->getPageSize();
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    public function addFilter(array $filter): static
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }
}
