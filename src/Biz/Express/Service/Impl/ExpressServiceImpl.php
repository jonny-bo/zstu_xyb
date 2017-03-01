<?php

namespace Biz\Express\Service\Impl;

use Codeages\Biz\Framework\Service\BaseService;
use Biz\User\Service\UserService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\ArrayToolkit;
use Biz\Express\Service\ExpressService;

class ExpressServiceImpl extends BaseService implements ExpressService
{
    public function getExpress($expressId)
    {
        return $this->getExpressDao()->get($expressId);
    }

    public function searchExpress($conditions, $orderBy, $start, $limit)
    {
        return $this->getExpressDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchExpressCount($conditions)
    {
        return $this->getExpressDao()->count($conditions);
    }

    public function createExpress($fields)
    {
        return $this->getExpressDao()->create($fields);
    }

    public function updateExpress($expressId, $fields)
    {
        return $this->getExpressDao()->update($expressId, $fields);
    }

    public function deleteExpress($expressId)
    {
        return $this->getExpressDao()->delete($expressId);
    }

    protected function getExpressDao()
    {
        return $this->biz->dao('Express:ExpressDao');
    }
}
