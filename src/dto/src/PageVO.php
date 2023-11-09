<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Annotation\ViewObject;
use MaliBoot\Utils\Collection;
use MaliBoot\Utils\Contract\Arrayable;

/**
 * @template T
 */
#[ViewObject]
class PageVO
{
    /**
     * @var int 总数
     */
    protected int $totalCount = 0;

    /**
     * @var int 每页数量
     */
    protected int $pageSize = 1;

    /**
     * @var int 当前页索引
     */
    protected int $pageIndex = 1;

    /**
     * The items.
     *
     * @var Collection<T>
     */
    private Collection $items;

    private string $itemType;

    /**
     * Create a new PageVO.
     * @param array|Collection<T> $items
     */
    public function __construct(Collection|array $items = [])
    {
        $this->items = $this->handleItems($items);
    }

    public function handleItems($items): Collection
    {
        if ($items instanceof Collection) {
            return $items;
        }

        foreach ($items as &$value) {
            $value = is_array($value) ? $this->handleItems($value) : (is_int($value) ? (string) $value : $value);
        }

        return new Collection($items);
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

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the ArrayList of items as a plain array.
     *
     * @return array<mixed, mixed>
     */
    public function toArray(bool $isZeroValFilter = false): array
    {
        $items = $this->items->map(function ($value) use ($isZeroValFilter) {
            if ($isZeroValFilter && empty($value)) {
                return false;
            }
            return $value instanceof Arrayable ? $value->toArray() : $value;
        });

        return [
            'pageSize' => $this->pageSize,
            'pageIndex' => $this->pageIndex,
            'totalCount' => $this->totalCount,
            'items' => $items,
        ];
    }

    /**
     * @return Collection<T>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param Collection<T> $items
     */
    public function setItems(Collection $items): static
    {
        $newItems = new Collection();
        foreach ($items as $item) {
            $newItem = $item;
            if (is_object($item) && ! is_subclass_of($item, $this->itemType)) {
                $newItem = call_user_func([$this->itemType, 'of'], $item->toArray());
            }

            $newItems->push($newItem);
        }
        $this->items = $newItems;
        return $this;
    }

    public function setItemType(string $itemType): static
    {
        $this->itemType = $itemType;
        return $this;
    }

    public static function ofPageVO(PageVO $sourcePageVO, ?string $itemType = null): static
    {
        $pageVO = new static();

        if ($itemType) {
            $pageVO->setItemType($itemType);
        }

        $pageVO->setItems($sourcePageVO->getItems())
            ->setPageIndex($sourcePageVO->getPageIndex())
            ->setPageSize($sourcePageVO->getPageSize())
            ->setTotalCount($sourcePageVO->getTotalCount());
        return $pageVO;
    }
}
