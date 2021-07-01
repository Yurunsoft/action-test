<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 关联右侧字段.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null $field 字段名
 */
#[\Attribute]
class JoinTo extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'field';

    public function __construct(?array $__data = null, ?string $field = null)
    {
        parent::__construct(...\func_get_args());
    }
}
