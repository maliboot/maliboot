<?php

declare(strict_types=1);

namespace MaliBoot\Utils\Contract;

use Hyperf\Contract\Arrayable as HyperfArrayable;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable extends HyperfArrayable
{
}
