<?php

namespace Topxia\Api\Filter;

use Codeages\Biz\Framework\Context\Biz;

abstract class BaseFilter
{
    protected $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }
}
