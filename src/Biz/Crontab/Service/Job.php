<?php

namespace Biz\Crontab\Service;

use Codeages\Biz\Framework\Context\Biz;

abstract class Job
{
    protected $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }

    abstract public function execute($params);
}
