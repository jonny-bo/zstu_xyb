<?php

namespace Biz\Express\Service\Impl;

use Biz\Common\BaseService;
use Biz\Express\Service\ExpressApplyService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;

class ExpressApplyServiceImpl extends BaseService implements ExpressApplyService
{
    public function getExpressApply($expressApplyId)
    {
        return $this->getExpressApplyDao()->get($expressApplyId);
    }

    public function searchExpressApply($conditions, $orderBy, $start, $limit)
    {
        return $this->getExpressApplyDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchExpressApplyCount($conditions)
    {
        return $this->getExpressApplyDao()->count($conditions);
    }

    public function createExpressApply($fields)
    {
        return $this->getExpressApplyDao()->create($fields);
    }

    public function updateExpressApply($expressApplyId, $fields)
    {
        return $this->getExpressApplyDao()->update($expressApplyId, $fields);
    }

    public function deleteExpressApply($expressApplyId)
    {
        return $this->getExpressApplyDao()->delete($expressApplyId);
    }

    public function findExpressApplyByExpressId($expressId)
    {
        return $this->getExpressApplyDao()->findByExpressId($expressId);
    }

    public function apply($expressId, $userId)
    {
        $express = $this->getExpressService()->getExpress($expressId);

        if (empty($express)) {
            throw new ResourceNotFoundException('express', $expressId, '订单不存在');
        }

        if ($express['status'] != 1) {
            throw new UnexpectedValueException('该订单已被认领！');
        }

        $apply = $this->getExpressApplyDao()->getByExpressIdAndUserId($expressId, $userId);

        if ($apply) {
            throw new UnexpectedValueException('不能重复申请该订单！');
        }

        $apply = array(
            'express_id' => $expressId,
            'user_id'    => $userId,
            'is_auth'    => 0
        );

        return $this->getExpressApplyDao()->create($apply);
    }

    public function auth($expressId, $userId)
    {
        $apply = $this->getExpressApplyDao()->getByExpressIdAndUserId($expressId, $userId);
        if (empty($apply)) {
            throw new UnexpectedValueException('不能授权给没申请的用户！');
        }
        $this->getExpressService()->orderExpress($expressId, $userId);
        $this->updateExpressApply($apply['id'], array('is_auth' => 1));
    }

    protected function getExpressApplyDao()
    {
        return $this->biz->dao('Express:ExpressApplyDao');
    }

    protected function getExpressService()
    {
        return $this->biz->service('Express:ExpressService');
    }
}
