<?php

declare(strict_types=1);

namespace MaliBoot\Utils;

use Hyperf\Collection\Collection as BaseCollection;
use Hyperf\Contract as HyperfContract;

/**
 * Collection.
 * @template TKey of array-key
 */
class Collection extends BaseCollection
{
    /**
     * 获取具有给定键值的数组.
     *
     * @param array<array-key, string>|string $value
     * @return BaseCollection<int, mixed>
     */
    public function pluck($value, ?string $key = null): BaseCollection
    {
        return $this->toBase()->pluck($value, $key);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return BaseCollection<int, TKey>
     */
    public function keys(): BaseCollection
    {
        return $this->toBase()->keys();
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return BaseCollection<int, mixed>
     */
    public function collapse(): BaseCollection
    {
        return $this->toBase()->collapse();
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param int $depth
     * @return BaseCollection<int, mixed>
     */
    public function flatten($depth = INF): BaseCollection
    {
        return $this->toBase()->flatten($depth);
    }

    /**
     * Flip the items in the collection.
     *
     * @return BaseCollection<TValue, TKey>
     */
    public function flip(): BaseCollection
    {
        return $this->toBase()->flip();
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @template TPadValue
     *
     * @param TPadValue $value
     * @return BaseCollection<int, TPadValue>
     */
    public function pad(int $size, $value): BaseCollection
    {
        return $this->toBase()->pad($size, $value);
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array<TKey, mixed>
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return $this->hasToArray($value) ? $value->toArray() : $value;
        }, $this->items);
    }

    protected function hasToArray($value): bool
    {
        return $value instanceof HyperfContract\Arrayable
            || $value instanceof Contract\Arrayable
            || (is_object($value) && method_exists($value, 'toArray'));
    }
}
