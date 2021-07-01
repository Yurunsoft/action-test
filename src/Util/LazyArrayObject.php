<?php

declare(strict_types=1);

namespace Imi\Util;

use ArrayObject;

/**
 * 同时可以作为数组和对象访问的类.
 */
class LazyArrayObject extends ArrayObject
{
    /**
     * @param mixed $input
     */
    public function __construct($input = [], int $flags = self::ARRAY_AS_PROPS, string $iteratorClass = 'ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
