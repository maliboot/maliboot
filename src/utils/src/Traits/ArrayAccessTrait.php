<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Traits;

trait ArrayAccessTrait
{
    /**
     * Determine if the given attribute exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        $method = getter($offset);
        return ! is_null($this->{$method}());
    }

    /**
     * Get the value for a given offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        $method = getter($offset);
        return $this->{$method}();
    }

    /**
     * Set the value for a given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $method = setter($offset);
        $this->{$method}($value);
    }

    /**
     * Unset the value for a given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }
}
