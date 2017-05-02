<?php

namespace Biz\Express\Service\Impl;

use Biz\Common\BaseService;
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
        $conditions = $this->perConditions($conditions);

        return $this->getExpressDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchExpressCount($conditions)
    {
        $conditions = $this->perConditions($conditions);

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
        $fields['title']        = $this->purifyHtml($fields['title']);
        $fields['detail']       = $this->purifyHtml($fields['detail']);
        $fields['status']       = 1;

        return $this->getExpressDao()->create($fields);
    }

    public function orderExpress($expressId, $userId)
    {
        $express = $this->checkExpress($expressId);

        if ($express['publisher_id'] == $userId) {
            throw new UnexpectedValueException('自己不能接自己的订单');
        }

        $lockName = "express_{$expressId}";
        $lock     = $this->getLock();
        $lock->get($lockName, 10);

        $express = $this->updateExpress($expressId, array('status' => 2, 'receiver_id' => $userId));

        $lock->release($lockName);

        return $express;
    }

    public function updateExpress($expressId, $fields)
    {
        $this->checkExpress($expressId);
        if (isset($fields['title'])) {
            $fields['title']  = $this->purifyHtml($fields['title']);
        }

        if (isset($fields['detail'])) {
            $fields['detail'] = $this->purifyHtml($fields['detail']);
        }

        return $this->getExpressDao()->update($expressId, $fields);
    }

    public function deleteExpress($expressId)
    {
        $this->checkExpress($expressId);

        return $this->getExpressDao()->delete($expressId);
    }

    public function confirmMyPublishExpress($expressId)
    {
        $user    = $this->getCurrentUser();
        $express = $this->getExpress($expressId);

        if ($user['id'] != $express['publisher_id']) {
            throw new UnexpectedValueException('不是发布者，不能确认收货');
        }

        if ($express['status'] != 3) {
            throw new UnexpectedValueException('该订单未送达，不能确认收货');
        }

        return $this->getExpressDao()->update($expressId, array('status' => 4));
    }

    public function confirmMyReceiveExpress($expressId)
    {
        $user    = $this->getCurrentUser();
        $express = $this->getExpress($expressId);

        if ($user['id'] != $express['receiver_id']) {
            throw new UnexpectedValueException('不是订单接收人，不能确认送到');
        }

        if ($express['status'] != 2) {
            throw new UnexpectedValueException('该订单不能确认送到');
        }

        return $this->getExpressDao()->update($expressId, array('status' => 3));
    }

    protected function perConditions($conditions)
    {
        if (isset($conditions['is_urgent']) && !empty($conditions['is_urgent'])) {
            $isUrgent = $conditions['is_urgent'];
        } else {
            $isUrgent = null;
        }

        $conditions =  array_filter($conditions);

        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            if ($conditions['keywordType'] == 'nickname') {
                $users = $this->getUserService()->searchUsers(array('nickname' => $conditions['keyword']), array(), 0, PHP_INT_MAX);
                $conditions['publishIds'] = empty($users) ? array(0) : ArrayToolkit::column($users, 'id');
            } else {
                $conditions[$conditions['keywordType']] = "%{$conditions['keyword']}%";
            }

            unset($conditions['keywordType']);
            unset($conditions['keyword']);
        }

        if (isset($conditions['startDate'])) {
            $conditions['startTime'] = strtotime($conditions['startDate']);
        }

        if (isset($conditions['endDate'])) {
            $conditions['endTime'] = strtotime($conditions['endDate']);
        }

        if ($isUrgent != null) {
            $conditions['is_urgent'] = $isUrgent;
        }

        return $conditions;
    }

    protected function checkExpress($expressId)
    {
        $express = $this->getExpress($expressId);

        if (!$express) {
            throw new ResourceNotFoundException('订单', $expressId);
        }

        if ($express['status'] != 1) {
            throw new UnexpectedValueException('该订单已经被别人接收');
        }

        return $express;
    }

    protected function getExpressDao()
    {
        return $this->biz->dao('Express:ExpressDao');
    }

    protected function getUserService()
    {
        return $this->biz->service('User:UserService');
    }
}
