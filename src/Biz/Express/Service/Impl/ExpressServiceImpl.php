<?php

namespace Biz\Express\Service\Impl;

use Biz\Common\BaseService;
use Biz\User\Service\UserService;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\UnexpectedValueException;
use Biz\Common\Exception\ResourceNotFoundException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\ArrayToolkit;
use Biz\Express\Service\ExpressService;

class ExpressServiceImpl extends BaseService implements ExpressService
{
    protected $requiredFields = array(
        'title', 'detail', 'offer'
    );

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
        if (!ArrayToolkit::requireds($fields, $this->requiredFields)) {
            throw new InvalidArgumentException('缺少必要参数');
        }

        $user = $this->getCurrentUser();

        if (empty($user->id)) {
            throw new AccessDeniedException('未登录用户，无权操作！');
        }

        $fields['publisher_id'] = $user['id'];

        return $this->getExpressDao()->create($fields);
    }

    public function orderExpress($expressId)
    {
        $lockName = "express_{$expressId}";
        $lock     = $this->getLock();
        $lock->get($lockName, 10);

        $express = $this->getExpress($expressId);

        if (!$express) {
            throw new ResourceNotFoundException('订单', $expressId);
        }

        if ($express['status'] != 0) {
            throw new UnexpectedValueException('该订单已经被别人抢先');
        }

        $user = $this->getCurrentUser();

        if ($express['publisher_id'] == $user['id']) {
            throw new UnexpectedValueException('自己不能接自己的订单');
        }

        $express = $this->updateExpress($expressId, array('status' => 1, 'receiver_id' => $user['id']));

        $lock->release($lockName);

        return $express;
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
